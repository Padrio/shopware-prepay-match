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
                // Payment methods to check
                // GER: Zu prüfende Zahlungsarten
                'property' => 'payment.id',
                'expression' => 'IN',
                'value' => $config->prepayPaymentMethods,
            ],
            [
                // Payment status to check
                // GER: Zu prüfende Bestellstati
                'property' => 'paymentStatus.id',
                'expression' => 'IN',
                'value' => $config->paymentIdsToCheck,
            ],
            [
                // OrderStatus to check
                // GER: Zu prüfende Bestellstati
                'property' => 'orderStatus.id',
                'expression' => 'IN',
                'value' => $config->statusIdsToCheck,
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