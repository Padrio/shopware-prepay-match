<?php

namespace PrepayMatch\Components\Banking\Adapter\Proxy;

use PrepayMatch\Components\Banking\Adapter\Proxy as ProxyAdapter;
use PrepayMatch\Components\DI\FactoryInterface;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Factory implements FactoryInterface
{
    /**
     * @return ProxyAdapter
     */
    public static function create()
    {
        $container = Shopware()->Container();
        $client = $container->get('pm_service_plugin.banking_proxy_client');
        return new ProxyAdapter($client);
    }
}