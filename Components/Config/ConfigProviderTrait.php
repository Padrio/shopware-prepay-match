<?php

namespace PrepayMatch\Components\Config;

use PrepayMatch\Components\DI\ContainerProviderTrait;
use PrepayMatch\Components\Config;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
trait ConfigProviderTrait
{
    use ContainerProviderTrait;

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->getContainer()->get('pm_service_plugin.config');
    }

}