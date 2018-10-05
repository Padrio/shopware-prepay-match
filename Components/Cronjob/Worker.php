<?php

namespace PrepayMatch\Components\Cronjob;

use PrepayMatch\Components\Banking\AdapterInterface;
use PrepayMatch\Components\Banking\Factory;
use PrepayMatch\Components\Config\ConfigProviderTrait;
use PrepayMatch\Components\Order\Repository;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Worker
{
    use ConfigProviderTrait;

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
        $orders = $this->repository->getList([], [], 0, 1);
        $adapter = $this->getAdapter();
        $collection = $adapter->fetchTransactions(new \DateTime('04.10.2018'));

        echo json_encode($orders) . PHP_EOL;

        return true;
    }

    /**
     * @return AdapterInterface
     */
    private function getAdapter()
    {
        $factory = new Factory();
        $config = $this->getConfig();
        return $factory($config->apiType);
    }
}