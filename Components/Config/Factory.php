<?php

namespace PrepayMatch\Components\Config;

use PrepayMatch\Components\DI\FactoryInterface;
use PrepayMatch\PrepayMatch;
use PrepayMatch\Components\Config;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Factory implements FactoryInterface
{
    const NAMESPACE = PrepayMatch::PLUGIN_NAME;

    /**
     * @return Config
     */
    public static function create()
    {
        $config = Shopware()->Config();

        return new Config(
            $config->getByNamespace(self::NAMESPACE, 'finTsApiType'),
            $config->getByNamespace(self::NAMESPACE, 'prepayPaymentMethod'),
            $config->getByNamespace(self::NAMESPACE, 'proxyHost'),
            $config->getByNamespace(self::NAMESPACE, 'proxySecret'),
            $config->getByNamespace(self::NAMESPACE, 'fintsServer'),
            $config->getByNamespace(self::NAMESPACE, 'fintsPort'),
            $config->getByNamespace(self::NAMESPACE, 'fintsBankCode'),
            $config->getByNamespace(self::NAMESPACE, 'fintsUsername'),
            $config->getByNamespace(self::NAMESPACE, 'fintsPin'),
            $config->getByNamespace(self::NAMESPACE, 'fintsAccountToCheck'),
            $config->getByNamespace(self::NAMESPACE, 'paymentIdsToCheck'),
            $config->getByNamespace(self::NAMESPACE, 'statusIdsToCheck'),
            $config->getByNamespace(self::NAMESPACE, 'statusOnMatch'),
            $config->getByNamespace(self::NAMESPACE, 'sendMailOnMatch')
        );
    }
}