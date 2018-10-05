<?php

namespace PrepayMatch\Components\Banking\Adapter;

use DateTime;
use Exception;
use Fhp\FinTs as NemiahFinTs;
use Padrio\BankingProxy\Transaction;
use PrepayMatch\Components\Banking\AdapterInterface;
use Shopware\Components\Logger;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class FinTs implements AdapterInterface
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $accountNumber;

    public function __construct(NemiahFinTs $finTs, Logger $logger)
    {
        $this->transaction = new Transaction($finTs, $logger);
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchTransactions(DateTime $from, DateTime $to)
    {
        try {
            return $this->transaction->getStatementCollection($this->accountNumber, $from, $to);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());

            return null;
        }
    }
}