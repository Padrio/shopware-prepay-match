<?php

namespace PrepayMatch\Components\Banking;

use Fhp\FinTs as NemiahFinTs;
use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use PrepayMatch\Components\Banking\Adapter\Proxy as ProxyAdapter;
use PrepayMatch\Components\Banking\Adapter\FinTs as FinTsAdapter;
use PrepayMatch\Components\Banking\Proxy\Client;
use PrepayMatch\Components\DI\ContainerProviderTrait;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Factory
{
    use ContainerProviderTrait;

    const TYPE_LOCAL = 'local';
    const TYPE_REMOTE = 'remote';

    /**
     * @param string $type
     * @return AdapterInterface
     */
    public function __invoke($type)
    {
        if($type === self::TYPE_REMOTE) {
            return $this->createProxyAdapter();
        }

        if($type === self::TYPE_LOCAL) {
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
        $client = $this->createFinTsClient();
        $logger = $this->getContainer()->get('pluginlogger');
        return new FinTsAdapter($client, $logger);
    }

    private function createFinTsClient()
    {
        $bag = $this->getContainer()->getParameterBag();
        $server = $bag->get('fintsServer');
        $port = $bag->get('fintsPort');
        $bankCode = $bag->get('fintsBankCode');
        $username = $bag->get('fintsUsername');
        $pin = $bag->get('fintsPin');

        $logger = $this->getContainer()->get('pluginlogger');
        return new NemiahFinTs($server, $port, $bankCode, $username, $pin, $logger);
    }

    /**
     * @return Client
     */
    private function createProxyHttpClient()
    {
        $host = $this->getContainer()->getParameter('proxyHost');
        $secret = $this->getContainer()->getParameter('proxySecret');

        $guzzleClient = new GuzzleClient([
            'base_uri' => $host,
        ]);

        return new Client($guzzleClient, $secret);
    }
}