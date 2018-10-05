<?php

namespace PrepayMatch\Components\Banking;

use InvalidArgumentException;
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
        return $this->getContainer()->get('pm_service_plugin.banking.adapter_proxy');
    }

    /**
     * @return AdapterInterface
     */
    private function createFinTsAdapter()
    {
        return $this->getContainer()->get('pm_service_plugin.banking.adapter_fints');
    }
}