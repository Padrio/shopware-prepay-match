<?php

namespace PrepayMatch\Components\DI;

use Shopware\Components\DependencyInjection\Container;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
trait ContainerProviderTrait
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->container = Shopware()->Container();
        }

        return $this->container;
    }
}