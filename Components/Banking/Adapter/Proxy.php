<?php

namespace PrepayMatch\Components\Banking\Adapter;

use DateTime;
use Padrio\BankingProxy\Model\StatementCollection;
use PrepayMatch\Components\Banking\AdapterInterface;
use PrepayMatch\Components\Banking\Proxy\Client as ProxyClient;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Proxy implements AdapterInterface
{
    /**
     * @var ProxyClient
     */
    private $client;

    /**
     * Unused. The accountNumber is known by the proxy.
     * @var string
     */
    private $accountNumber;

    public function __construct(ProxyClient $client)
    {
        $this->client = $client;
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
    public function fetchTransactions(DateTime $from, DateTime $to = null)
    {
        $transactions = $this->client->getTransactions($from, $to);
        if(!$transactions) {
            return null;
        }

        return StatementCollection::createFromArray($transactions['transactions']);
    }
}