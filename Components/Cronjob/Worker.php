<?php

namespace PrepayMatch\Components\Cronjob;

use Doctrine\ORM\EntityManagerInterface;
use Padrio\BankingProxy\Shared\Model\Transaction;
use PrepayMatch\Components\Banking\AdapterInterface;
use PrepayMatch\Components\Config\ConfigProviderTrait;
use PrepayMatch\Components\Database\DoctrineProviderTrait;
use PrepayMatch\Components\Order\Filter\Criteria;
use PrepayMatch\Components\Order\Filter\Sorting;
use PrepayMatch\Components\Order\Repository;
use PrepayMatch\Models\MatchedTransaction;
use Shopware\Models\Order\Order;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Worker
{

    use ConfigProviderTrait;
    use DoctrineProviderTrait;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function match()
    {
        $orders = $this->repository->getList(Criteria::create(), Sorting::create(), 0, 30);
        if (empty($orders)) {
            return;
        }

        $adapter = $this->getConfiguredAdapter();
        list($from, $to) = $this->detectStartAndEnd($orders);

        $collection = $adapter->fetchTransactions($from, $to);
        $statement = array_pop(array_reverse($collection->statements));
        $repository = $this->getMatchedTransactionRepository();

        $entity = $this->getOrderRepository()->find(57);

        foreach ($orders as $order) {

            /** @var Transaction $transaction */
            foreach ($statement->transactions as $transaction) {

                $matched = $repository->findByHash($transaction->getHash());
                if (!$matched) {
                    $matched = new MatchedTransaction();
                }

                if (!$matched->isMatchedName()) {
                    // Match against customer name
                }

                if (!$matched->isMatchedOrderId()) {
                    // Match against order id in description
                }

                if ($matched->isPartlyCompleted()) {
                    /** @var Order $entity */
                    $entity = $this->getOrderRepository()->find($order['id']);

                }


            }
        }

        return;
    }

    private function detectStartAndEnd(array $orders)
    {
        $first = end($orders);
        $last = array_pop(array_reverse($orders));

        return [
            $first['orderTime'],
            $last['orderTime'],
        ];
    }


    /**
     * @return AdapterInterface
     */
    private function getConfiguredAdapter()
    {
        return $this->getContainer()->get('pm_service_plugin.banking.configured_adapter');
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('models');
    }

}