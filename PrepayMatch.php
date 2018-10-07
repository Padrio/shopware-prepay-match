<?php

namespace PrepayMatch;

use PrepayMatch\Bootstrap\Database;
use PrepayMatch\Components\Cronjob\Worker;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware_Components_Cron_CronJob;

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
}

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
class PrepayMatch extends Plugin
{

    const PLUGIN_NAME = 'PrepayMatch';

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_CronJob_PrepayMatch' => 'onCheckPrepayCronJob',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function install(InstallContext $context)
    {
        $database = new Database(
            $this->container->get('models')
        );

        $database->install();
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(UninstallContext $context)
    {
        $database = new Database(
            $this->container->get('models')
        );

        if ($context->keepUserData()) {
            return;
        }

        $database->uninstall();
    }

    /**
     * @param Shopware_Components_Cron_CronJob $job
     *
     * @return bool
     */
    public function onCheckPrepayCronJob(Shopware_Components_Cron_CronJob $job)
    {
        return $this->getCronJobWorker()->match();
    }

    /**
     * @return Worker
     */
    private function getCronJobWorker()
    {
        /** @var Worker $worker */
        $worker = $this->container->get('pm_service_plugin.worker');

        return $worker;
    }
}