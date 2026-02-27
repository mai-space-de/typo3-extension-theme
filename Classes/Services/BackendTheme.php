<?php

namespace Maispace\Theme\Services;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendTheme
{
    private array $backend_extension_settings = [
        'backendFavicon',
        'backendLogo',
        'loginBackgroundImage',
        'loginFootnote',
        'loginHighlightColor',
        'loginLogo',
        'loginLogoAlt'
    ];

    public function registerBackendTheme(): void
    {
        $globalBackendConfig = GeneralUtility::makeInstance(ActiveExtensionConfigurationLoader::class)
            ->getMergedConfigurationByFilename('BackendTheme');

        foreach ($globalBackendConfig as $settingKey => $settingValue) {
            if (in_array($settingKey, $this->backend_extension_settings)) {
                $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend'][$settingKey] = $settingValue;
            }
        }
    }
}