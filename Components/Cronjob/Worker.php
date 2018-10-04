<?php

namespace PrepayMatch\Components\Cronjob;

use PrepayMatch\Components\Order\Repository;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\DependencyInjection\ContainerAwareInterface;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
class Worker implements ContainerAwareInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Container
     */
    private $container;

    public function setContainer(Container $Container = null)
    {
        $this->container = $Container;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        if(!$this->repository) {
            $repository = $this->container->get('pm_service_plugin.order_repository');
            $this->repository = $repository;
        }

        return $this->repository;
    }

    public function match()
    {
        echo 'hurr durr im doing more things!' . PHP_EOL;
        $orders = $this->getRepository()->getList();
        var_dump($orders);

        return true;
    }
}