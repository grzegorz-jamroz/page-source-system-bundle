<?php

declare(strict_types=1);

namespace Ifrost\Bundle\PageSourceSystemBundle\Utilities;

use PageSourceSystem\Domain\Seo;
use PageSourceSystem\Repository\SettingsRepository;
use PageSourceSystem\Setting\AbstractGeneral;
use PageSourceSystem\Setting\AbstractLanguages;
use PageSourceSystem\Setting\BaseGeneral;
use PageSourceSystem\Setting\BaseLanguages;
use PageSourceSystem\Storage\ComponentStorage;
use Ramsey\Uuid\Uuid;
use SimpleStorageSystem\Document\Exception\FileNotExists;

class StorageInitiator
{
    public function __construct(
        private string $directory,
        private SettingsRepository $settingsRepository
    ) {
    }

    public function init(): void
    {
        try {
            $this->settingsRepository->getLanguages();
        } catch (FileNotExists) {
            $this->generateDefaultLanguages();
        }

        try {
            $this->settingsRepository->getGeneral();
        } catch (FileNotExists) {
            $this->generateDefaultGeneral();
        }
    }

    protected function generateDefaultLanguages(): void
    {
        $this->settingsRepository->getSettingStorage(AbstractLanguages::getTypename())->overwrite(
            (new BaseLanguages(
                'pl',
                [
                    'pl',
                    'en',
                ],
                [
                    [
                        'label' => 'Polski',
                        'value' => 'pl',
                    ],
                    [
                        'label' => 'English',
                        'value' => 'en',
                    ]
                ]
            ))->jsonSerialize()
        );
    }

    protected function generateDefaultGeneral(): void
    {
        $primarySeo = [];

        foreach ($this->settingsRepository->getSupportedLanguages() as $language) {
            $uuid = (string) Uuid::uuid4();
            $primarySeo[$language] = $uuid;
            $seo = Seo::createFromArray([
                'uuid' => $uuid,
                'language' => $language,
            ]);
            $componentStorage = new ComponentStorage(
                $this->directory,
                $language,
                $uuid
            );
            $componentStorage->overwrite(
                $seo->jsonSerialize(),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        }

        $this->settingsRepository->getSettingStorage(AbstractGeneral::getTypename())->overwrite(
            (new BaseGeneral($primarySeo))->jsonSerialize()
        );
    }
}
