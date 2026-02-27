<?php

namespace Maispace\Theme\Services;

use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ActiveExtensionConfigurationLoader
{
    private PackageManager $packageManager;

    public function __construct(?PackageManager $packageManager = null)
    {
        $this->packageManager = $packageManager ?? GeneralUtility::makeInstance(PackageManager::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function getMergedConfigurationByFilename(string $filename): array
    {
        $configuration = [];
        foreach ($this->packageManager->getActivePackages() as $activePackage) {
            $configurationFile = $activePackage->getPackagePath() . 'Configuration/' . $filename . '.php';
            if (file_exists($configurationFile)) {
                $configArray = require $configurationFile;
                if (is_array($configArray)) {
                    ArrayUtility::mergeRecursiveWithOverrule($configuration, $configArray);
                }
            }
        }

        // @phpstan-ignore-next-line
        return (array)$configuration;
    }
}
