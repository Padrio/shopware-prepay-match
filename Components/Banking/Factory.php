<?php

namespace PrepayMatch\Components\Banking;

use Fhp\FinTs as NemiahFinTs;
use InvalidArgumentException;
use PrepayMatch\Components\Banking\Adapter\FinTs as FinTsAdapter;
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
        return $this->getContainer()->get('pm_service_plug.banking.adapter_proxy');
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
}