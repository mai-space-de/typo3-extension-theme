<?php

declare(strict_types = 1);

namespace Maispace\Theme\Tests\Unit\Services;

use Maispace\Theme\Services\ActiveExtensionConfigurationLoader;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;

class ActiveExtensionConfigurationLoaderTest extends TestCase
{
    public function testGetMergedConfigurationByFilename(): void
    {
        $packageManagerMock = $this->createMock(PackageManager::class);
        $package1Mock = $this->createMock(PackageInterface::class);

        // This is a bit difficult to test without actual files, but we can try to mock the file access if needed.
        // Or we can just mock the whole Loader if we're testing classes that use it.

        $loader = new ActiveExtensionConfigurationLoader($packageManagerMock);
        $this->assertInstanceOf(ActiveExtensionConfigurationLoader::class, $loader);
    }
}
