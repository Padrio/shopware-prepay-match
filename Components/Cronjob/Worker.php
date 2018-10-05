<?php

namespace PrepayMatch\Components\Cronjob;

use PrepayMatch\Components\DI\ContainerProviderTrait;
use PrepayMatch\Components\Order\Repository;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Worker
{
    use ContainerProviderTrait;

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
        echo json_encode($orders);

        return true;
    }


}