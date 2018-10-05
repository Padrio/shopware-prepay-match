<?php

namespace PrepayMatch\Components\Banking;

use DateTime;
use Padrio\BankingProxy\Shared\Model\StatementCollection;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
interface AdapterInterface
{
    /**
     * @param DateTime $from
     * @param DateTime $to
     *
     * @return StatementCollection|null
     */
    public function fetchTransactions(DateTime $from, DateTime $to = null);

    /**
     * @param string $accountNumber
     *
     * @return void
     */
    public function setAccountNumber($accountNumber);
}