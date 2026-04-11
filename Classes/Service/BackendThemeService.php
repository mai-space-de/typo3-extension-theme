<?php

namespace Maispace\MaiTheme\Service;

use Maispace\MaiBase\Utility\ActiveExtensionConfigurationLoader;

class BackendThemeService
{
    const backend_extension_settings = [
        'backendFavicon',
        'backendLogo',
        'loginBackgroundImage',
        'loginFootnote',
        'loginHighlightColor',
        'loginLogo',
        'loginLogoAlt',
    ];

    public static function applyTheme(): void
    {
        $themeConfiguration = ActiveExtensionConfigurationLoader::getMergedConfigurationByFilename('BackendTheme');
        foreach ($themeConfiguration as $settingKey => $settingValue) {
            if (in_array($settingKey, self::backend_extension_settings, true)) {
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