<?php

namespace PrepayMatch\Components\Banking\Adapter\FinTs;

use PrepayMatch\Components\DI\FactoryInterface;
use PrepayMatch\Components\Banking\Adapter\FinTs as FinTsAdapter;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Factory implements FactoryInterface
{
    /**
     * @return FinTsAdapter
     */
    public static function create()
    {
        $config = Shopware()->Config();
        $container = Shopware()->Container();
        $client = $container->get('pm_service_plugin.fhp.fints');
        $logger = $container->get('pluginlogger');

        $adapter = new FinTsAdapter($client, $logger);
        $adapter->setAccountNumber($config->fintsAccountToCheck);

        return $adapter;
    }
}