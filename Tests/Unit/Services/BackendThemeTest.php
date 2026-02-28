<?php

declare(strict_types=1);

namespace Maispace\Theme\Tests\Unit\Services;

use Maispace\Theme\Services\ActiveExtensionConfigurationLoader;
use Maispace\Theme\Services\BackendTheme;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendThemeTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $originalTypo3ConfVars;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalTypo3ConfVars = $GLOBALS['TYPO3_CONF_VARS'] ?? [];
        $GLOBALS['TYPO3_CONF_VARS'] = [];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $GLOBALS['TYPO3_CONF_VARS'] = $this->originalTypo3ConfVars;
        GeneralUtility::purgeInstances();
    }

    private function injectLoader(array $config): void
    {
        $loaderMock = $this->createMock(ActiveExtensionConfigurationLoader::class);
        $loaderMock->method('getMergedConfigurationByFilename')->willReturn($config);
        GeneralUtility::addInstance(ActiveExtensionConfigurationLoader::class, $loaderMock);
    }

    // ── Registration ───────────────────────────────────────────────────────────

    public function testRegisterBackendThemeWithEmptyConfigDoesNothing(): void
    {
        $this->injectLoader([]);

        (new BackendTheme())->registerBackendTheme();

        $this->assertArrayNotHasKey('EXTENSIONS', $GLOBALS['TYPO3_CONF_VARS']);
    }

    public function testRegisterBackendLogoIsApplied(): void
    {
        $this->injectLoader(['backendLogo' => 'EXT:theme/Icons/logo.svg']);

        (new BackendTheme())->registerBackendTheme();

        $this->assertSame(
            'EXT:theme/Icons/logo.svg',
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['backendLogo']
        );
    }

    public function testRegisterBackendFaviconIsApplied(): void
    {
        $this->injectLoader(['backendFavicon' => 'EXT:theme/Icons/favicon.ico']);

        (new BackendTheme())->registerBackendTheme();

        $this->assertSame(
            'EXT:theme/Icons/favicon.ico',
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['backendFavicon']
        );
    }

    public function testAllSevenAllowedSettingsAreApplied(): void
    {
        $config = [
            'backendFavicon'       => 'EXT:theme/Icons/favicon.ico',
            'backendLogo'          => 'EXT:theme/Icons/logo.svg',
            'loginBackgroundImage' => 'EXT:theme/Images/login-bg.jpg',
            'loginFootnote'        => 'Powered by TYPO3',
            'loginHighlightColor'  => '#0078d4',
            'loginLogo'            => 'EXT:theme/Icons/login-logo.svg',
            'loginLogoAlt'         => 'My Brand',
        ];
        $this->injectLoader($config);

        (new BackendTheme())->registerBackendTheme();

        $backend = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend'];
        foreach ($config as $key => $value) {
            $this->assertArrayHasKey($key, $backend, "Expected '{$key}' to be registered.");
            $this->assertSame($value, $backend[$key]);
        }
    }

    public function testUnknownSettingsAreIgnored(): void
    {
        $this->injectLoader([
            'unknownSetting'  => 'should-be-ignored',
            'anotherInvalid'  => 'also-ignored',
            'backendLogo'     => 'EXT:theme/Icons/logo.svg',
        ]);

        (new BackendTheme())->registerBackendTheme();

        $backend = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend'] ?? [];
        $this->assertArrayNotHasKey('unknownSetting', $backend);
        $this->assertArrayNotHasKey('anotherInvalid', $backend);
        $this->assertSame('EXT:theme/Icons/logo.svg', $backend['backendLogo']);
    }

    public function testOnlyAllowedSettingsReachGlobals(): void
    {
        $this->injectLoader([
            'loginHighlightColor' => '#ff0000',
            'injectedKey'         => 'malicious',
        ]);

        (new BackendTheme())->registerBackendTheme();

        $backend = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend'] ?? [];
        $this->assertCount(1, $backend, 'Only allowed settings should be written to GLOBALS.');
        $this->assertSame('#ff0000', $backend['loginHighlightColor']);
    }

    public function testExistingGlobalsArePreservedForOtherExtensions(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['other_ext']['someSetting'] = 'existing-value';
        $this->injectLoader(['backendLogo' => 'EXT:theme/Icons/logo.svg']);

        (new BackendTheme())->registerBackendTheme();

        $this->assertSame(
            'existing-value',
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['other_ext']['someSetting'],
            'Settings from other extensions must not be overwritten.'
        );
    }
}
