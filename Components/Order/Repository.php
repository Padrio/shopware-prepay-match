<?php

namespace PrepayMatch\Components\Order;

use DateTime;
use Shopware\Bundle\AttributeBundle\Repository\SearchCriteria;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\DependencyInjection\ContainerAwareInterface;
use Shopware\Models\Order\Order;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Repository implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Shopware\Models\Order\Repository
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $Container = null)
    {
        $this->container = $Container;
    }

    /*
     * @return \Shopware\Models\Order\Repository
     */
    protected function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = Shopware()->Models()->getRepository(Order::class);
        }

        return $this->repository;
    }

    public function getList($filter = [], $sort = [], $offset = 0, $limit = 30)
    {
        $sort = $this->resolveSortParameter($sort);

        if ($this->container->getParameter('shopware.es.backend.enabled')) {
            $repository = $this->container->get('shopware_attribute.order_repository');
            $criteria = $this->createCriteria();
            $result = $repository->search($criteria);

            $total = $result->getCount();
            $ids = array_column($result->getData(), 'id');
        } else {
            $searchResult = $this->getRepository()->search($offset, $limit, $filter, $sort);
            $total = $searchResult['total'];
            $ids = array_column($searchResult['orders'], 'id');
        }

        $orders = $this->getRepository()->getList($ids);
        $documents = $this->getRepository()->getDocuments($ids);
        $details = $this->getRepository()->getDetails($ids);
        $payments = $this->getRepository()->getPayments($ids);

        $orders = $this->assignAssociation($orders, $documents, 'documents');
        $orders = $this->assignAssociation($orders, $details, 'details');
        $orders = $this->assignAssociation($orders, $payments, 'paymentInstances');

        $numbers = [];
        foreach ($orders as $order) {
            $temp = array_column($order['details'], 'articleNumber');
            $numbers = array_merge($numbers, (array) $temp);
        }

        $result = [];
        foreach ($ids as $id) {
            if (!array_key_exists($id, $orders)) {
                continue;
            }

            $order = $orders[$id];
            $order['locale'] = $order['languageSubShop']['locale'];
            $result[] = $order;
        }

        return [
            'success' => true,
            'data' => $result,
            'total' => $total,
        ];
    }

    /**
     * @param array[] $sorts
     *
     * @return array[]
     */
    private function resolveSortParameter($sorts)
    {
        if (empty($sorts)) {
            return [
                ['property' => 'orders.orderTime', 'direction' => 'DESC'],
            ];
        }

        $resolved = [];
        foreach ($sorts as $sort) {
            $direction = $sort['direction'] ?: 'ASC';
            switch (true) {
                // Custom sort field for customer email
                case $sort['property'] === 'customerEmail':
                    $resolved[] = ['property' => 'customer.email', 'direction' => $direction];
                    break;

                // Custom sort field for customer name
                case $sort['property'] === 'customerName':
                    $resolved[] = ['property' => 'billing.lastName', 'direction' => $direction];
                    $resolved[] = ['property' => 'billing.firstName', 'direction' => $direction];
                    $resolved[] = ['property' => 'billing.company', 'direction' => $direction];
                    break;

                // Contains no sql prefix? add orders as default prefix
                case strpos($sort['property'], '.') === false:
                    $resolved[] = ['property' => 'orders.' . $sort['property'], 'direction' => $direction];
                    break;

                // Already prefixed with an alias?
                default:
                    $resolved[] = $sort;
            }
        }

        return $resolved;
    }

    /**
     * @param int   $offset
     * @param int   $limit
     * @param array $filter
     *
     * @return SearchCriteria
     */
    private function createCriteria($offset = 0, $limit = 30, array $filter = [])
    {
        $criteria = new SearchCriteria(Order::class);

        $criteria->offset = $offset;
        $criteria->limit = $limit;
        $conditions = $filter;

        $mapped = [];
        foreach ($conditions as $condition) {
            if ($condition['property'] === 'free') {
                $criteria->term = $condition['value'];
                continue;
            }

            if ($condition['property'] === 'billing.countryId') {
                $condition['property'] = 'billingCountryId';
            } elseif ($condition['property'] === 'shipping.countryId') {
                $condition['property'] = 'shippingCountryId';
            } else {
                $name = explode('.', $condition['property']);
                $name = array_pop($name);
                $condition['property'] = $name;
            }

            if ($condition['property'] === 'to') {
                $condition['value'] = (new DateTime($condition['value']))->format('Y-m-d');
                $condition['property'] = 'orderTime';
                $condition['expression'] = '<=';
            }

            if ($condition['property'] === 'from') {
                $condition['value'] = (new DateTime($condition['value']))->format('Y-m-d');
                $condition['property'] = 'orderTime';
                $condition['expression'] = '>=';
            }

            $mapped[] = $condition;

        }

        $criteria->conditions = $mapped;
        return $criteria;
    }

    private function assignAssociation(array $orders, $associations, $arrayKey)
    {
        foreach ($orders as &$order) {
            $order[$arrayKey] = [];
        }

        foreach ($associations as $association) {
            $id = $association['orderId'];
            $orders[$id][$arrayKey][] = $association;
        }

        return $orders;
    }
}