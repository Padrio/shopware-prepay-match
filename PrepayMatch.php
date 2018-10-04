<?php

namespace PrepayMatch;

use Shopware\Components\Plugin;
use Shopware_Components_Cron_CronJob;

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
class PrepayMatch extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_CronJob_PrepayMatch' => 'onCheckPrepayCronJob'
        ];
    }

    public function onCheckPrepayCronJob(Shopware_Components_Cron_CronJob $job)
    {
        $this->getCronJobWorker()->match();
        return true;
    }

    /**
     * @return Components\Cronjob\Worker
     */
    private function getCronJobWorker()
    {
        return $this->container->get('pm_service_plugin.worker');
    }
}