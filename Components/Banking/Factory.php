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
    private static $adapterTypeMapping = [
        Config::TYPE_LOCAL => 'pm_service_plugin.banking.adapter_fints',
        Config::TYPE_REMOTE => 'pm_service_plugin.banking.adapter_proxy',
    ];

    /**
     * @return AdapterInterface
     */
    public static function create()
    {
        $config = self::getConfig();
        $container = Shopware()->Container();
        if(isset(self::$adapterTypeMapping[$config->apiType])) {
            return $container->get(self::$adapterTypeMapping[$config->apiType]);
        }

        throw new InvalidArgumentException('Unknown $type when creating adapter-instance - did you configured it? - '. $config->apiType);
    }

    /**
     * @return Config
     */
    private static function getConfig()
    {
        return Shopware()->Container()->get('pm_service_plugin.config');
    }
}