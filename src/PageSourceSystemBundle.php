<?php

declare(strict_types=1);

namespace Ifrost\Bundle\PageSourceSystemBundle;

use Ifrost\Bundle\PageSourceSystemBundle\Utilities\StorageInitiator;
use PageSourceSystem\Repository\SettingsRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PageSourceSystemBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        /** @var SettingsRepository $settingsRepository */
        $settingsRepository = $container->get(SettingsRepository::class);
        $directory = $container->getParameter('app_data_dir');

        if (!is_string($directory)) {
            throw new \Exception('Parameter app_data_dir is not set as string.');
        }

        (new StorageInitiator(
            $directory,
            $settingsRepository,
        ))->init();
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
