<?php

namespace PrepayMatch\Components\Cronjob;

use PrepayMatch\Components\Banking\AdapterInterface;
use PrepayMatch\Components\Config\ConfigProviderTrait;
use PrepayMatch\Components\Order\Filter\Criteria;
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
        $filter = Criteria::create();
        $orders = $this->repository->getList($filter, [], 0, 30);

        if(empty($orders)) {
            return;
        }

        $adapter = $this->getConfiguredAdapter();
        $collection = $adapter->fetchTransactions(new \DateTime('04.10.2018'));

        echo json_encode($orders) . PHP_EOL;
        return;
    }


    /**
     * @return AdapterInterface
     */
    private function getConfiguredAdapter()
    {
        return $this->getContainer()->get('pm_service_plugin.banking.configured_adapter');
    }
}