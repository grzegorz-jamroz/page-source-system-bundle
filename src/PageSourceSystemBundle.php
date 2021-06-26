<?php

declare(strict_types=1);

namespace Ifrost\Bundle\PageSourceSystemBundle;

use Ifrost\Bundle\PageSourceSystemBundle\Utilities\StorageInitiator;
use Ifrost\PageSourceComponents\SettingCollection;
use PageSourceSystem\Repository\SettingsRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PageSourceSystemBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        dump($this->extension->getConfiguration());
        $directory = $this->container->getParameter('page_source_system.app_data_dir');

        if (!is_string($directory)) {
            throw new \Exception('Parameter page_source_system.app_data_dir is not set as string.');
        }

        (new StorageInitiator(
            $directory,
            new SettingsRepository($directory, new SettingCollection()),
        ))->init();
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
