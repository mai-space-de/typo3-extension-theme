<?php

declare(strict_types=1);

namespace Maispace\Theme\Tests\Unit\EventListener;

use Maispace\Theme\EventListener\FrontendAssetConfigurationsListener;
use Maispace\Theme\Services\ActiveExtensionConfigurationLoader;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Page\Event\BeforeJavaScriptsRenderingEvent;
use TYPO3\CMS\Core\Page\Event\BeforeStylesheetsRenderingEvent;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FrontendAssetConfigurationsListenerTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $originalRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalRequest = $GLOBALS['TYPO3_REQUEST'] ?? [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $GLOBALS['TYPO3_REQUEST'] = $this->originalRequest;
        GeneralUtility::purgeInstances();
    }

    private function makeFrontendRequest(string $siteIdentifier = 'default'): ServerRequest
    {
        $request = new ServerRequest('GET', 'https://example.com/');
        $request = $request->withAttribute('applicationType', ApplicationType::FRONTEND);

        $siteMock = $this->createMock(Site::class);
        $siteMock->method('getIdentifier')->willReturn($siteIdentifier);

        return $request->withAttribute('site', $siteMock);
    }

    private function makeBackendRequest(): ServerRequest
    {
        $request = new ServerRequest('GET', 'https://example.com/typo3/');
        return $request->withAttribute('applicationType', ApplicationType::BACKEND);
    }

    private function injectLoader(array $config): void
    {
        $loaderMock = $this->createMock(ActiveExtensionConfigurationLoader::class);
        $loaderMock->method('getMergedConfigurationByFilename')->willReturn($config);
        GeneralUtility::addInstance(ActiveExtensionConfigurationLoader::class, $loaderMock);
    }

    private function makeStylesheetsEvent(AssetCollector $collector): BeforeStylesheetsRenderingEvent
    {
        $eventMock = $this->createMock(BeforeStylesheetsRenderingEvent::class);
        $eventMock->method('getAssetCollector')->willReturn($collector);
        return $eventMock;
    }

    private function makeJavaScriptsEvent(AssetCollector $collector): BeforeJavaScriptsRenderingEvent
    {
        $eventMock = $this->createMock(BeforeJavaScriptsRenderingEvent::class);
        $eventMock->method('getAssetCollector')->willReturn($collector);
        return $eventMock;
    }

    // ── Early-return guards ────────────────────────────────────────────────────

    public function testNoAssetsRegisteredWhenGlobalsRequestIsAbsent(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = null;
        $this->injectLoader(['frontend' => ['my-style' => ['source' => 'EXT:ext/style.css']]]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->never())->method('addStyleSheet');

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    public function testNoAssetsRegisteredWhenRequestHasNoApplicationType(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = new ServerRequest('GET', 'https://example.com/');
        $this->injectLoader(['frontend' => ['my-style' => ['source' => 'EXT:ext/style.css']]]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->never())->method('addStyleSheet');

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    // ── Frontend stylesheet registration ───────────────────────────────────────

    public function testFrontendStyleSheetIsRegistered(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest();
        $this->injectLoader([
            'frontend' => [
                'my-style' => ['source' => 'EXT:ext/style.css'],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->once())
            ->method('addStyleSheet')
            ->with('my-style', 'EXT:ext/style.css', [], []);

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    public function testFrontendStyleSheetWithAttributesAndOptionsIsRegistered(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest();
        $this->injectLoader([
            'frontend' => [
                'my-style' => [
                    'source'     => 'EXT:ext/style.css',
                    'attributes' => ['media' => 'print'],
                    'options'    => ['priority' => true],
                ],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->once())
            ->method('addStyleSheet')
            ->with('my-style', 'EXT:ext/style.css', ['media' => 'print'], ['priority' => true]);

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    public function testMultipleFrontendStyleSheetsAreAllRegistered(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest();
        $this->injectLoader([
            'frontend' => [
                'style-one' => ['source' => 'EXT:ext/one.css'],
                'style-two' => ['source' => 'EXT:ext/two.css'],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->exactly(2))->method('addStyleSheet');

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    // ── Frontend JavaScript registration ──────────────────────────────────────

    public function testFrontendJavaScriptIsRegistered(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest();
        $this->injectLoader([
            'frontend' => [
                'my-script' => ['source' => 'EXT:ext/app.js'],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->once())
            ->method('addJavaScript')
            ->with('my-script', 'EXT:ext/app.js', [], []);

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setJavaScriptConfiguration($this->makeJavaScriptsEvent($collector));
    }

    public function testFrontendJavaScriptWithAttributesAndOptionsIsRegistered(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest();
        $this->injectLoader([
            'frontend' => [
                'my-script' => [
                    'source'     => 'EXT:ext/app.js',
                    'attributes' => ['defer' => 'defer'],
                    'options'    => ['useNonce' => true],
                ],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->once())
            ->method('addJavaScript')
            ->with('my-script', 'EXT:ext/app.js', ['defer' => 'defer'], ['useNonce' => true]);

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setJavaScriptConfiguration($this->makeJavaScriptsEvent($collector));
    }

    // ── Backend assets not leaked into frontend ────────────────────────────────

    public function testBackendStyleSheetsAreNotRegisteredForFrontend(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest();
        $this->injectLoader([
            'backend' => [
                'backend-style' => ['source' => 'EXT:ext/backend.css'],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->never())->method('addStyleSheet');

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    public function testBackendJavaScriptsAreNotRegisteredForFrontend(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest();
        $this->injectLoader([
            'backend' => [
                'backend-script' => ['source' => 'EXT:ext/backend.js'],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->never())->method('addJavaScript');

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setJavaScriptConfiguration($this->makeJavaScriptsEvent($collector));
    }

    // ── Site-identifier filtering ──────────────────────────────────────────────

    public function testAssetWithMatchingSiteIdentifierIsRegistered(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest('my-site');
        $this->injectLoader([
            'frontend' => [
                'site-style' => [
                    'source'          => 'EXT:ext/style.css',
                    'site-identifier' => 'my-site',
                ],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->once())->method('addStyleSheet');

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    public function testAssetWithNonMatchingSiteIdentifierIsNotRegistered(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest('site-a');
        $this->injectLoader([
            'frontend' => [
                'site-style' => [
                    'source'          => 'EXT:ext/style.css',
                    'site-identifier' => 'site-b',
                ],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->never())->method('addStyleSheet');

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    public function testAssetWithoutSiteIdentifierIsRegisteredForAllSites(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest('any-site');
        $this->injectLoader([
            'frontend' => [
                'global-style' => ['source' => 'EXT:ext/global.css'],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->once())
            ->method('addStyleSheet')
            ->with('global-style', 'EXT:ext/global.css', [], []);

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    public function testMixedSiteIdentifierAssetsAreFilteredCorrectly(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeFrontendRequest('site-a');
        $this->injectLoader([
            'frontend' => [
                'global-style'  => ['source' => 'EXT:ext/global.css'],
                'site-a-style'  => ['source' => 'EXT:ext/a.css', 'site-identifier' => 'site-a'],
                'site-b-style'  => ['source' => 'EXT:ext/b.css', 'site-identifier' => 'site-b'],
            ],
        ]);

        $registered = [];
        $collector = $this->createMock(AssetCollector::class);
        $collector->method('addStyleSheet')
            ->willReturnCallback(static function (string $id) use (&$registered): void {
                $registered[] = $id;
            });

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));

        $this->assertContains('global-style', $registered);
        $this->assertContains('site-a-style', $registered);
        $this->assertNotContains('site-b-style', $registered);
    }

    // ── Backend context ────────────────────────────────────────────────────────

    public function testBackendAssetsAreRegisteredInBackendContext(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeBackendRequest();
        $this->injectLoader([
            'backend' => [
                'backend-style' => ['source' => 'EXT:ext/backend.css'],
            ],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->once())
            ->method('addStyleSheet')
            ->with('backend-style', 'EXT:ext/backend.css', [], []);

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }

    public function testFrontendAssetsAreNotRegisteredInBackendContext(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->makeBackendRequest();
        $this->injectLoader([
            'frontend' => [
                'frontend-style' => ['source' => 'EXT:ext/frontend.css'],
            ],
            'backend' => [],
        ]);

        $collector = $this->createMock(AssetCollector::class);
        $collector->expects($this->never())->method('addStyleSheet');

        $listener = new FrontendAssetConfigurationsListener();
        $listener->setStyleSheetConfiguration($this->makeStylesheetsEvent($collector));
    }
}
