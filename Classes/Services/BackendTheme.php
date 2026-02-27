<?php

namespace Maispace\Theme\Services;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendTheme
{
    /**
     * @var array<int, string>
     */
    private array $backend_extension_settings = [
        'backendFavicon',
        'backendLogo',
        'loginBackgroundImage',
        'loginFootnote',
        'loginHighlightColor',
        'loginLogo',
        'loginLogoAlt',
    ];

    public function registerBackendTheme(): void
    {
        $globalBackendConfig = GeneralUtility::makeInstance(ActiveExtensionConfigurationLoader::class)
            ->getMergedConfigurationByFilename('BackendTheme');

        foreach ($globalBackendConfig as $settingKey => $settingValue) {
            if (in_array($settingKey, $this->backend_extension_settings, true)) {
                $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
                $extensions = (array)($typo3ConfVars['EXTENSIONS'] ?? []);
                $backend = (array)($extensions['backend'] ?? []);
                $backend[$settingKey] = $settingValue;
                $extensions['backend'] = $backend;
                $typo3ConfVars['EXTENSIONS'] = $extensions;
                $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;
            }
        }
    }
}
