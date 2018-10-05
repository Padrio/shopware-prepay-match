<?php

namespace PrepayMatch\Components\Banking;

use InvalidArgumentException;
use PrepayMatch\Components\Config;
use PrepayMatch\Components\DI\FactoryInterface;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Factory implements FactoryInterface
{
    /**
     * @return AdapterInterface
     */
    public static function create()
    {
        $container = Shopware()->Container();

        /** @var Config $config */
        $config = $container->get('pm_service_plugin.config');
        if($config->apiType === Config::TYPE_REMOTE) {
            return $container->get('pm_service_plugin.banking.adapter_proxy');
        }

        if($config->apiType === Config::TYPE_LOCAL) {
            return $container->get('pm_service_plugin.banking.adapter_fints');
        }

        throw new InvalidArgumentException('Unknown $type when creating adapter-instance: '. $config->apiType);
    }
}