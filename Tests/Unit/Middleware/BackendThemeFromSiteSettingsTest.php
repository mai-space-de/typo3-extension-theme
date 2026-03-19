<?php

declare(strict_types = 1);

namespace Maispace\Theme\Tests\Unit\Middleware;

use Maispace\Theme\Middleware\BackendThemeFromSiteSettings;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteSettings;
use TYPO3\CMS\Core\Site\SiteFinder;

final class BackendThemeFromSiteSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure clean state for backend config
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend'] = [];
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']);
        parent::tearDown();
    }

    public function testClassCanBeInstantiated(): void
    {
        $siteFinderMock = $this->createMock(SiteFinder::class);
        $middleware = new BackendThemeFromSiteSettings($siteFinderMock);

        self::assertInstanceOf(BackendThemeFromSiteSettings::class, $middleware);
    }

    public function testProcessDelegatesToHandler(): void
    {
        $siteFinderMock = $this->createMock(SiteFinder::class);
        $siteFinderMock->method('getAllSites')->willReturn([]);

        $middleware = new BackendThemeFromSiteSettings($siteFinderMock);

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->expects(self::once())
            ->method('handle')
            ->with($requestMock)
            ->willReturn($responseMock);

        $result = $middleware->process($requestMock, $handlerMock);

        self::assertSame($responseMock, $result);
    }

    public function testNoSitesDoesNotModifyGlobals(): void
    {
        $siteFinderMock = $this->createMock(SiteFinder::class);
        $siteFinderMock->method('getAllSites')->willReturn([]);

        $middleware = new BackendThemeFromSiteSettings($siteFinderMock);

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->method('handle')->willReturn($responseMock);

        $middleware->process($requestMock, $handlerMock);

        self::assertSame([], $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']);
    }

    public function testSiteWithoutLoginLogoDoesNotApplySettings(): void
    {
        $siteSettings = SiteSettings::createFromSettingsTree([
            'maispace' => [
                'theme' => [
                    'backend' => [
                        'logo' => 'EXT:theme/Resources/Public/Icons/logo.svg',
                    ],
                ],
            ],
        ]);

        $siteMock = $this->createMock(Site::class);
        $siteMock->method('getSettings')->willReturn($siteSettings);

        $siteFinderMock = $this->createMock(SiteFinder::class);
        $siteFinderMock->method('getAllSites')->willReturn([$siteMock]);

        $middleware = new BackendThemeFromSiteSettings($siteFinderMock);

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->method('handle')->willReturn($responseMock);

        $middleware->process($requestMock, $handlerMock);

        self::assertSame([], $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']);
    }

    public function testSiteWithLoginLogoAppliesBackendSettings(): void
    {
        $siteSettings = SiteSettings::createFromSettingsTree([
            'maispace' => [
                'theme' => [
                    'backend' => [
                        'loginLogo'           => 'EXT:theme/Resources/Public/Images/login.png',
                        'loginLogoAlt'        => 'My Project',
                        'loginHighlightColor' => '#e11d48',
                        'loginFootnote'       => '© 2026 Acme Corp',
                    ],
                ],
            ],
        ]);

        $siteMock = $this->createMock(Site::class);
        $siteMock->method('getSettings')->willReturn($siteSettings);

        $siteFinderMock = $this->createMock(SiteFinder::class);
        $siteFinderMock->method('getAllSites')->willReturn([$siteMock]);

        $middleware = new BackendThemeFromSiteSettings($siteFinderMock);

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->method('handle')->willReturn($responseMock);

        $middleware->process($requestMock, $handlerMock);

        $backendConfig = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend'];
        self::assertSame('EXT:theme/Resources/Public/Images/login.png', $backendConfig['loginLogo']);
        self::assertSame('My Project', $backendConfig['loginLogoAlt']);
        self::assertSame('#e11d48', $backendConfig['loginHighlightColor']);
        self::assertSame('© 2026 Acme Corp', $backendConfig['loginFootnote']);
    }

    public function testSkipsEmptySettingValues(): void
    {
        $siteSettings = SiteSettings::createFromSettingsTree([
            'maispace' => [
                'theme' => [
                    'backend' => [
                        'loginLogo' => 'EXT:theme/Resources/Public/Images/login.png',
                        'logo'      => '',
                        'favicon'   => '',
                    ],
                ],
            ],
        ]);

        $siteMock = $this->createMock(Site::class);
        $siteMock->method('getSettings')->willReturn($siteSettings);

        $siteFinderMock = $this->createMock(SiteFinder::class);
        $siteFinderMock->method('getAllSites')->willReturn([$siteMock]);

        $middleware = new BackendThemeFromSiteSettings($siteFinderMock);

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->method('handle')->willReturn($responseMock);

        $middleware->process($requestMock, $handlerMock);

        $backendConfig = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend'];
        self::assertSame('EXT:theme/Resources/Public/Images/login.png', $backendConfig['loginLogo']);
        self::assertArrayNotHasKey('backendLogo', $backendConfig);
        self::assertArrayNotHasKey('backendFavicon', $backendConfig);
    }

    public function testPicksFirstSiteWithLoginLogo(): void
    {
        $siteSettingsWithoutLogo = SiteSettings::createFromSettingsTree([
            'maispace' => [
                'theme' => [
                    'backend' => [
                        'logo' => 'EXT:theme/Resources/Public/Icons/logo.svg',
                    ],
                ],
            ],
        ]);

        $siteSettingsWithLogo = SiteSettings::createFromSettingsTree([
            'maispace' => [
                'theme' => [
                    'backend' => [
                        'loginLogo'     => 'EXT:site2/Resources/Public/Images/login.png',
                        'loginFootnote' => '© Site 2',
                    ],
                ],
            ],
        ]);

        $site1Mock = $this->createMock(Site::class);
        $site1Mock->method('getSettings')->willReturn($siteSettingsWithoutLogo);

        $site2Mock = $this->createMock(Site::class);
        $site2Mock->method('getSettings')->willReturn($siteSettingsWithLogo);

        $siteFinderMock = $this->createMock(SiteFinder::class);
        $siteFinderMock->method('getAllSites')->willReturn([$site1Mock, $site2Mock]);

        $middleware = new BackendThemeFromSiteSettings($siteFinderMock);

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->method('handle')->willReturn($responseMock);

        $middleware->process($requestMock, $handlerMock);

        $backendConfig = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend'];
        self::assertSame('EXT:site2/Resources/Public/Images/login.png', $backendConfig['loginLogo']);
        self::assertSame('© Site 2', $backendConfig['loginFootnote']);
    }

    public function testSiteFinderExceptionDoesNotBreakBackend(): void
    {
        $siteFinderMock = $this->createMock(SiteFinder::class);
        $siteFinderMock->method('getAllSites')
            ->willThrowException(new \RuntimeException('Site configuration unavailable'));

        $middleware = new BackendThemeFromSiteSettings($siteFinderMock);

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock->expects(self::once())
            ->method('handle')
            ->willReturn($responseMock);

        $result = $middleware->process($requestMock, $handlerMock);

        self::assertSame($responseMock, $result);
        self::assertSame([], $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']);
    }
}
