<?php

namespace PrepayMatch\Components\Order\Filter;

use PrepayMatch\Components\Config;
use PrepayMatch\Components\Config\ConfigProviderTrait;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Criteria
{
    use ConfigProviderTrait;

    /**
     * Ready made criteria/filter to fetch orders which needs to be processed
     *
     * More about filters can be read here:
     * https://developers.shopware.com/developers-guide/rest-api/#filter,-sort,-limit,-offset
     *
     * @return array
     */
    public static function create()
    {
        $config = self::getConfig();

        return [
            [
                'property' => 'payment.id',
                'value' => $config->prepayPaymentMethod,
            ],
        ];
    }

    /**
     * @return Config
     */
    private static function getConfig()
    {
        return Shopware()->Container()->get('pm_service_plugin.config');
    }
}