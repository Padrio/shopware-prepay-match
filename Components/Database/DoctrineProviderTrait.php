<?php

namespace PrepayMatch\Components\Database;

use Doctrine\ORM\EntityManagerInterface;
use PrepayMatch\Models\MatchedTransaction;
use PrepayMatch\Models\MatchedTransactionRepository;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Repository;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
trait DoctrineProviderTrait
{
    /**
     * @return EntityManagerInterface
     */
    abstract function getEntityManager();

    /**
     * @return MatchedTransactionRepository
     */
    private function getMatchedTransactionRepository()
    {
        return $this->getEntityManager()->getRepository(MatchedTransaction::class);
    }

    /**
     * @return Repository
     */
    private function getOrderRepository()
    {
        return Shopware()->Models()->getRepository(Order::class);
    }
}