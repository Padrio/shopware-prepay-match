<?php

namespace PrepayMatch\Components\Banking\Proxy\Client;

use GuzzleHttp\Client as GuzzleClient;
use PrepayMatch\Components\Banking\Proxy\Client;
use PrepayMatch\Components\DI\FactoryInterface;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Factory implements FactoryInterface
{
    /**
     * @return Client
     */
    public static function create()
    {
        $config = Shopware()->Config();
        $guzzleClient = new GuzzleClient([
            'base_url' => $config->proxyHost,
        ]);

        return new Client($guzzleClient, $config->proxySecret);
    }
}