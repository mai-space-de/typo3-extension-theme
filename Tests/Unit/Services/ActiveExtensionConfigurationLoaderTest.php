<?php

declare(strict_types=1);

namespace Maispace\Theme\Tests\Unit\Services;

use Maispace\Theme\Services\ActiveExtensionConfigurationLoader;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;

class ActiveExtensionConfigurationLoaderTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . '/typo3-loader-test-' . uniqid('', true);
        mkdir($this->tempDir . '/Configuration', 0777, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->removeDirectory($this->tempDir);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach ((array) scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function makePackageMock(string $packagePath): PackageInterface
    {
        $mock = $this->createMock(PackageInterface::class);
        $mock->method('getPackagePath')->willReturn($packagePath . '/');
        return $mock;
    }

    private function makeLoader(PackageInterface ...$packages): ActiveExtensionConfigurationLoader
    {
        $packageManager = $this->createMock(PackageManager::class);
        $packageManager->method('getActivePackages')->willReturn($packages);
        return new ActiveExtensionConfigurationLoader($packageManager);
    }

    // ── Instantiation ──────────────────────────────────────────────────────────

    public function testInstantiation(): void
    {
        $loader = $this->makeLoader();
        $this->assertInstanceOf(ActiveExtensionConfigurationLoader::class, $loader);
    }

    // ── Empty / missing config ─────────────────────────────────────────────────

    public function testReturnsEmptyArrayWhenNoPackagesRegistered(): void
    {
        $loader = $this->makeLoader();
        $this->assertSame([], $loader->getMergedConfigurationByFilename('StyleSheets'));
    }

    public function testReturnsEmptyArrayWhenPackageHasNoConfigFile(): void
    {
        $loader = $this->makeLoader($this->makePackageMock($this->tempDir));
        $this->assertSame([], $loader->getMergedConfigurationByFilename('StyleSheets'));
    }

    public function testIgnoresConfigFilesThatReturnNull(): void
    {
        file_put_contents($this->tempDir . '/Configuration/StyleSheets.php', '<?php return null;');

        $loader = $this->makeLoader($this->makePackageMock($this->tempDir));
        $this->assertSame([], $loader->getMergedConfigurationByFilename('StyleSheets'));
    }

    public function testIgnoresConfigFilesThatReturnAString(): void
    {
        file_put_contents($this->tempDir . '/Configuration/StyleSheets.php', '<?php return "invalid";');

        $loader = $this->makeLoader($this->makePackageMock($this->tempDir));
        $this->assertSame([], $loader->getMergedConfigurationByFilename('StyleSheets'));
    }

    // ── Single-package loading ─────────────────────────────────────────────────

    public function testLoadsFrontendStyleSheetsFromPackage(): void
    {
        file_put_contents($this->tempDir . '/Configuration/StyleSheets.php', <<<'PHP'
        <?php return [
            'frontend' => [
                'my-style' => ['source' => 'EXT:my_ext/Resources/Public/StyleSheet/style.css'],
            ],
        ];
        PHP);

        $result = $this->makeLoader($this->makePackageMock($this->tempDir))
            ->getMergedConfigurationByFilename('StyleSheets');

        $this->assertArrayHasKey('frontend', $result);
        $this->assertArrayHasKey('my-style', $result['frontend']);
        $this->assertSame('EXT:my_ext/Resources/Public/StyleSheet/style.css', $result['frontend']['my-style']['source']);
    }

    public function testLoadsFrontendJavaScriptsFromPackage(): void
    {
        file_put_contents($this->tempDir . '/Configuration/JavaScripts.php', <<<'PHP'
        <?php return [
            'frontend' => [
                'my-script' => ['source' => 'EXT:my_ext/Resources/Public/JavaScript/app.js'],
            ],
        ];
        PHP);

        $result = $this->makeLoader($this->makePackageMock($this->tempDir))
            ->getMergedConfigurationByFilename('JavaScripts');

        $this->assertArrayHasKey('frontend', $result);
        $this->assertArrayHasKey('my-script', $result['frontend']);
        $this->assertSame('EXT:my_ext/Resources/Public/JavaScript/app.js', $result['frontend']['my-script']['source']);
    }

    public function testLoadsBackendThemeFromPackage(): void
    {
        file_put_contents($this->tempDir . '/Configuration/BackendTheme.php', <<<'PHP'
        <?php return [
            'backendLogo' => 'EXT:my_ext/Resources/Public/Icons/logo.svg',
            'loginHighlightColor' => '#0078d4',
        ];
        PHP);

        $result = $this->makeLoader($this->makePackageMock($this->tempDir))
            ->getMergedConfigurationByFilename('BackendTheme');

        $this->assertSame('EXT:my_ext/Resources/Public/Icons/logo.svg', $result['backendLogo']);
        $this->assertSame('#0078d4', $result['loginHighlightColor']);
    }

    public function testLoadsAssetsWithAttributesAndOptions(): void
    {
        file_put_contents($this->tempDir . '/Configuration/StyleSheets.php', <<<'PHP'
        <?php return [
            'frontend' => [
                'my-style' => [
                    'source' => 'EXT:my_ext/style.css',
                    'attributes' => ['media' => 'print'],
                    'options' => ['priority' => true],
                ],
            ],
        ];
        PHP);

        $result = $this->makeLoader($this->makePackageMock($this->tempDir))
            ->getMergedConfigurationByFilename('StyleSheets');

        $asset = $result['frontend']['my-style'];
        $this->assertSame(['media' => 'print'], $asset['attributes']);
        $this->assertSame(['priority' => true], $asset['options']);
    }

    // ── Multi-package merging ──────────────────────────────────────────────────

    public function testMergesStyleSheetsFromMultiplePackages(): void
    {
        $dir2 = sys_get_temp_dir() . '/typo3-loader-test-' . uniqid('', true);
        mkdir($dir2 . '/Configuration', 0777, true);

        try {
            file_put_contents($this->tempDir . '/Configuration/StyleSheets.php', <<<'PHP'
            <?php return ['frontend' => ['style-one' => ['source' => 'EXT:ext1/style1.css']]];
            PHP);
            file_put_contents($dir2 . '/Configuration/StyleSheets.php', <<<'PHP'
            <?php return ['frontend' => ['style-two' => ['source' => 'EXT:ext2/style2.css']]];
            PHP);

            $result = $this->makeLoader(
                $this->makePackageMock($this->tempDir),
                $this->makePackageMock($dir2),
            )->getMergedConfigurationByFilename('StyleSheets');

            $this->assertArrayHasKey('style-one', $result['frontend']);
            $this->assertArrayHasKey('style-two', $result['frontend']);
        } finally {
            $this->removeDirectory($dir2);
        }
    }

    public function testLaterPackageOverridesEarlierPackageForSameKey(): void
    {
        $dir2 = sys_get_temp_dir() . '/typo3-loader-test-' . uniqid('', true);
        mkdir($dir2 . '/Configuration', 0777, true);

        try {
            file_put_contents($this->tempDir . '/Configuration/StyleSheets.php', <<<'PHP'
            <?php return ['frontend' => ['my-style' => ['source' => 'EXT:ext1/original.css']]];
            PHP);
            file_put_contents($dir2 . '/Configuration/StyleSheets.php', <<<'PHP'
            <?php return ['frontend' => ['my-style' => ['source' => 'EXT:ext2/override.css']]];
            PHP);

            $result = $this->makeLoader(
                $this->makePackageMock($this->tempDir),
                $this->makePackageMock($dir2),
            )->getMergedConfigurationByFilename('StyleSheets');

            $this->assertSame('EXT:ext2/override.css', $result['frontend']['my-style']['source']);
        } finally {
            $this->removeDirectory($dir2);
        }
    }

    public function testPackageWithoutConfigFileDoesNotBlockOtherPackages(): void
    {
        $emptyDir = sys_get_temp_dir() . '/typo3-loader-test-' . uniqid('', true);
        mkdir($emptyDir . '/Configuration', 0777, true);

        try {
            file_put_contents($this->tempDir . '/Configuration/StyleSheets.php', <<<'PHP'
            <?php return ['frontend' => ['my-style' => ['source' => 'EXT:ext1/style.css']]];
            PHP);

            $result = $this->makeLoader(
                $this->makePackageMock($emptyDir),
                $this->makePackageMock($this->tempDir),
            )->getMergedConfigurationByFilename('StyleSheets');

            $this->assertArrayHasKey('my-style', $result['frontend']);
        } finally {
            $this->removeDirectory($emptyDir);
        }
    }

    public function testConfigWithSiteIdentifierIsPreserved(): void
    {
        file_put_contents($this->tempDir . '/Configuration/StyleSheets.php', <<<'PHP'
        <?php return [
            'frontend' => [
                'site-specific' => [
                    'source' => 'EXT:my_ext/style.css',
                    'site-identifier' => 'my-site',
                ],
            ],
        ];
        PHP);

        $result = $this->makeLoader($this->makePackageMock($this->tempDir))
            ->getMergedConfigurationByFilename('StyleSheets');

        $this->assertSame('my-site', $result['frontend']['site-specific']['site-identifier']);
    }
}
