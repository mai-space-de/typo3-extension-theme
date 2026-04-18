<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Service;

use Maispace\MaiTheme\Event\AfterBackendThemeAppliedEvent;
use Maispace\MaiTheme\Service\BackendThemeService;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Unit tests for {@see BackendThemeService}. Since `apply()` calls
 * {@see \Maispace\MaiBase\Utility\ActiveExtensionConfigurationLoader::getMergedConfigurationByFilename()}
 * (which depends on TYPO3's PackageManager), these tests focus on the pure
 * behavior we can verify without bootstrapping TYPO3: the constant surface
 * and the list of allowed backend setting keys.
 */
final class BackendThemeServiceTest extends TestCase
{
    public function testBackendExtensionSettingsContainsExpectedKeys(): void
    {
        $expected = [
            'backendFavicon',
            'backendLogo',
            'loginBackgroundImage',
            'loginFootnote',
            'loginHighlightColor',
            'loginLogo',
            'loginLogoAlt',
        ];

        self::assertSame($expected, BackendThemeService::BACKEND_EXTENSION_SETTINGS);
    }

    public function testConstructorAcceptsNullEventDispatcher(): void
    {
        $service = new BackendThemeService(null);
        self::assertInstanceOf(BackendThemeService::class, $service);
    }

    public function testConstructorAcceptsEventDispatcher(): void
    {
        $dispatcher = new class implements EventDispatcherInterface {
            public function dispatch(object $event): object
            {
                return $event;
            }
        };

        $service = new BackendThemeService($dispatcher);
        self::assertInstanceOf(BackendThemeService::class, $service);
    }

    public function testAfterBackendThemeAppliedEventExposesAndMutatesSettings(): void
    {
        $event = new AfterBackendThemeAppliedEvent(['backendLogo' => '/logo.svg']);
        self::assertSame(['backendLogo' => '/logo.svg'], $event->getAppliedSettings());

        $event->setAppliedSettings(['backendLogo' => '/override.svg']);
        self::assertSame(['backendLogo' => '/override.svg'], $event->getAppliedSettings());
    }
}
