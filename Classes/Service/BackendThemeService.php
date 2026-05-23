<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Service;

use Maispace\MaiBase\Utility\ActiveExtensionConfigurationLoader;
use Maispace\MaiTheme\Event\AfterBackendThemeAppliedEvent;
use Maispace\MaiTheme\Exception\InvalidThemeConfigurationException;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Applies backend theme settings (logo, favicon, colors, login background, ...)
 * from `Configuration/BackendTheme.php` files shipped by any loaded extension,
 * merged by {@see ActiveExtensionConfigurationLoader}.
 *
 * Provides both:
 *  - a static `applyTheme()` entry point (for `ext_localconf.php` where DI
 *    is not yet available), and
 *  - a DI-friendly `apply()` instance method that fires
 *    {@see AfterBackendThemeAppliedEvent}.
 */
class BackendThemeService implements SingletonInterface
{
    public const BACKEND_EXTENSION_SETTINGS = [
        'backendFavicon',
        'backendLogo',
        'loginBackgroundImage',
        'loginFootnote',
        'loginHighlightColor',
        'loginLogo',
        'loginLogoAlt',
    ];

    public function __construct(
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
    ) {}

    /**
     * Legacy static entry point retained for `ext_localconf.php`.
     */
    public static function applyTheme(): void
    {
        GeneralUtility::makeInstance(self::class)->apply();
    }

    /**
     * @return array<string, mixed> settings that were actually applied
     */
    public function apply(): array
    {
        $themeConfiguration = ActiveExtensionConfigurationLoader::getMergedConfigurationByFilename('BackendTheme');

        if (!is_array($themeConfiguration)) {
            throw new InvalidThemeConfigurationException(
                'BackendTheme configuration must be an array.',
                1_736_000_001,
            );
        }

        $applied = [];
        foreach ($themeConfiguration as $settingKey => $settingValue) {
            if (!in_array($settingKey, self::BACKEND_EXTENSION_SETTINGS, true)) {
                continue;
            }

            $typo3ConfVars = (array) ($GLOBALS['TYPO3_CONF_VARS'] ?? []);
            $extensions = (array) ($typo3ConfVars['EXTENSIONS'] ?? []);
            $backend = (array) ($extensions['backend'] ?? []);
            $backend[$settingKey] = $settingValue;
            $extensions['backend'] = $backend;
            $typo3ConfVars['EXTENSIONS'] = $extensions;
            $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;

            $applied[$settingKey] = $settingValue;
        }

        if ($this->eventDispatcher !== null) {
            $event = new AfterBackendThemeAppliedEvent($applied);
            $this->eventDispatcher->dispatch($event);
            $applied = $event->getAppliedSettings();
        }

        return $applied;
    }
}
