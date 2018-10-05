<?php

namespace PrepayMatch\Components\Banking;

use Fhp\FinTs as NemiahFinTs;
use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use PrepayMatch\Components\Banking\Adapter\Proxy as ProxyAdapter;
use PrepayMatch\Components\Banking\Adapter\FinTs as FinTsAdapter;
use PrepayMatch\Components\Banking\Proxy\Client;
use PrepayMatch\Components\Config\ConfigProviderTrait;
use PrepayMatch\Components\Config;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Factory
{
    use ConfigProviderTrait;

    /**
     * @param string $type
     * @return AdapterInterface
     */
    public function __invoke($type)
    {
        if($type === Config::TYPE_REMOTE) {
            return $this->createProxyAdapter();
        }

        if($type === Config::TYPE_LOCAL) {
            return $this->createFinTsAdapter();
        }

        throw new InvalidArgumentException('Unknown $type when creating adapter-instance: '. $type);
    }

    /**
     * @return AdapterInterface
     */
    private function createProxyAdapter()
    {
        $client = $this->createProxyHttpClient();
        return new ProxyAdapter($client);
    }

    /**
     * @return AdapterInterface
     */
    private function createFinTsAdapter()
    {
        $config = $this->getConfig();
        $client = $this->createFinTsClient();
        $logger = $this->getContainer()->get('pluginlogger');

        $adapter = new FinTsAdapter($client, $logger);
        $adapter->setAccountNumber($config->fintsAccountToCheck);
        return $adapter;
    }

    private function createFinTsClient()
    {
        $logger = $this->getContainer()->get('pluginlogger');
        $config = $this->getConfig();

        return new NemiahFinTs(
            $config->fintsServer,
            $config->fintsPort,
            $config->fintsBankCode,
            $config->fintsUsername,
            $config->fintsPin,
            $logger
        );
    }

    /**
     * @return Client
     */
    private function createProxyHttpClient()
    {
        $config = $this->getConfig();
        $guzzleClient = new GuzzleClient([
            'base_url' => $config->proxyHost,
        ]);

        return new Client($guzzleClient, $config->proxySecret);
    }
}