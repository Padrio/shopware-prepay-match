<?php

namespace PrepayMatch\Components\Cronjob;

use Doctrine\ORM\EntityManager;
use Padrio\BankingProxy\Model\Transaction;
use PrepayMatch\Components\Banking\AdapterInterface;
use PrepayMatch\Components\Config\ConfigProviderTrait;
use PrepayMatch\Components\Database\DoctrineProviderTrait;
use PrepayMatch\Components\Order\Filter\Criteria;
use PrepayMatch\Components\Order\Repository;
use PrepayMatch\Components\Order\Sorting;
use PrepayMatch\Models\MatchedTransaction;
use Shopware\Models\Order\Billing;
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

        foreach ($orders as $order) {

            /** @var Order $order */
            $order = $this->getOrderRepository()->find($order['id']);

            /** @var Transaction $transaction */
            foreach ($statement->transactions as $transaction) {

                $matched = $repository->findByHash($transaction->getHash());
                if (!$matched) {
                    $matched = new MatchedTransaction();
                }

                if (!$matched->isMatchedFirstName()) {
                    $hasMatchedFirstName = $this->matchFirstName($order->getBilling(), $transaction);
                    $matched->setMatchedFirstName($hasMatchedFirstName);
                }

                if(!$matched->isMatchedLastName()) {
                    $hasMatchedLastName = $this->matchLastName($order->getBilling(), $transaction);
                    $matched->setMatchedLastName($hasMatchedLastName);
                }

                if (!$matched->isMatchedOrderId()) {
                    $hasMatchedNumber = $this->matchOrderNumber($order->getNumber(), $transaction);
                    $matched->setMatchedOrderId($hasMatchedNumber);
                }

                if ($matched->isPartlyCompleted()) {
                    /** @var Order $entity */
                    $entity = $this->getOrderRepository()->find($order['id']);
                    $entity->setPaymentStatus($this->getConfiguredStatus());

                    $this->getEntityManager()->persist($matched);
                    $this->getEntityManager()->persist($entity);
                    $this->getEntityManager()->flush();
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
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return Shopware()->Models();
    }

    private function getConfiguredStatus()
    {
        $status = $this->getConfig()->statusOnMatch;
        return $this->getStatusRepository()->find($status);
    }

    /**
     * Check if the sender from the Transaction matches against customer name
     *
     * @param Billing     $billing
     * @param Transaction $transaction
     *
     * @return false|int
     */
    private function matchFirstName(Billing $billing, Transaction $transaction)
    {
        return (bool) preg_match('\b'. $billing->getFirstName() . '\b', $transaction->bookingText, $matches);
    }

    /**
     * Check if the FirstName
     *
     * @param Billing     $billing
     * @param Transaction $transaction
     *
     * @return bool
     */
    private function matchLastName(Billing $billing, Transaction $transaction)
    {
        return (bool) preg_match('\b'. $billing->getLastName() . '\b', $transaction->bookingText, $matches);
    }

    /**
     * @param int           $number
     * @param Transaction   $transaction
     *
     * @return bool
     */
    private function matchOrderNumber($number, Transaction $transaction)
    {
        return (bool) preg_match('\b' . $number . '\b', $transaction->description);
    }
}