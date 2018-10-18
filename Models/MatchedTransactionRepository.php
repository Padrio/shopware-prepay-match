<?php

namespace PrepayMatch\Models;

use Doctrine\ORM\EntityRepository;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
class MatchedTransactionRepository extends EntityRepository
{
    /**
     * @param string $hash
     *
     * @return null|MatchedTransaction
     */
    public function findByHash($hash)
    {
        return $this->findOneBy(['transactionHash' => $hash]);
    }
}