<?php

namespace PrepayMatch\Components\Database;

use Doctrine\ORM\EntityManagerInterface;
use PrepayMatch\Models\MatchedTransaction;
use PrepayMatch\Models\MatchedTransactionRepository;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Repository;
use Shopware\Models\Order\Status;
use Shopware\Models\Payment\Payment;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
trait DoctrineProviderTrait
{
    /**
     * @return MatchedTransactionRepository
     */
    private function getMatchedTransactionRepository()
    {
        return Shopware()->Models()->getRepository(MatchedTransaction::class);
    }

    /**
     * @return Repository
     */
    private function getOrderRepository()
    {
        return Shopware()->Models()->getRepository(Order::class);
    }

    /**
     * @return \Shopware\Models\Payment\Repository
     */
    private function getPaymentRepository()
    {
        return Shopware()->Models()->getRepository(Payment::class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getStatusRepository()
    {
        return Shopware()->Models()->getRepository(Status::class);
    }
}