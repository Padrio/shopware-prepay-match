<?php

namespace PrepayMatch\Components\Order;

use DateTime;
use PrepayMatch\Components\DI\ContainerProviderTrait;
use Shopware\Bundle\AttributeBundle\Repository\SearchCriteria;
use Shopware\Models\Order\Order;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Repository
{
    use ContainerProviderTrait;

    /**
     * @var \Shopware\Models\Order\Repository
     */
    private $repository;

    /*
     * @return \Shopware\Models\Order\Repository
     */
    private function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = Shopware()->Models()->getRepository(Order::class);
        }

        return $this->repository;
    }

    /**
     * @param array $filter
     * @param array $sort
     * @param int   $offset
     * @param int   $limit
     *
     * @return array[]
     */
    public function getList($filter = [], $sort = [], $offset = 0, $limit = 30)
    {
        $sort = $this->resolveSortParameter($sort);
        if ($this->getContainer()->getParameter('shopware.es.backend.enabled')) {
            $repository = $this->getContainer()->get('shopware_attribute.order_repository');
            $criteria = $this->createCriteria();
            $result = $repository->search($criteria);

            $ids = array_column($result->getData(), 'id');
        } else {
            $searchResult = $this->getRepository()->search($offset, $limit, $filter, $sort);
            $ids = array_column($searchResult['orders'], 'id');
        }

        return $this->getRepository()->getList($ids);
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
}