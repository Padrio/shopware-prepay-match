<?php

namespace PrepayMatch\Components\Banking\FhpFinTs;

use PrepayMatch\Components\DI\FactoryInterface;
use Fhp\FinTs as BaseFinTs;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Factory implements FactoryInterface
{
    /**
     * @return BaseFinTs
     */
    public static function create()
    {
        $container = Shopware()->Container();
        $config = Shopware()->Config();

        $logger = $container->get('pluginlogger');

        return new BaseFinTs(
            $config->fintsServer,
            $config->fintsPort,
            $config->fintsBankCode,
            $config->fintsUsername,
            $config->fintsPin,
            $logger
        );
    }
}