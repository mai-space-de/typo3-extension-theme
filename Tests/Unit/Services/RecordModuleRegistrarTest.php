<?php

declare(strict_types = 1);

namespace Maispace\Theme\Tests\Unit\Services;

use Maispace\Theme\Services\ActiveExtensionConfigurationLoader;
use Maispace\Theme\Services\RecordModuleRegistrar;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RecordModuleRegistrarTest extends TestCase
{
    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    public function testBuildModuleRegistrationsReturnsEmptyArrayWhenNoConfigurationExists(): void
    {
        $loaderMock = $this->createMock(ActiveExtensionConfigurationLoader::class);
        $loaderMock->method('getMergedConfigurationByFilename')
            ->with('RecordModules')
            ->willReturn([]);

        GeneralUtility::addInstance(ActiveExtensionConfigurationLoader::class, $loaderMock);

        $registrar = new RecordModuleRegistrar();
        $result = $registrar->buildModuleRegistrations();

        $this->assertSame([], $result);
    }

    public function testBuildModuleRegistrationsBuildsModuleForValidTable(): void
    {
        $GLOBALS['TCA']['sys_category'] = [
            'ctrl' => [
                'title'    => 'Categories',
                'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/mimetypes/mimetypes-x-sys_category.svg',
            ],
        ];

        $loaderMock = $this->createMock(ActiveExtensionConfigurationLoader::class);
        $loaderMock->method('getMergedConfigurationByFilename')
            ->with('RecordModules')
            ->willReturn([
                'sys_category' => [
                    'pids'    => '1,2',
                    'sorting' => 10,
                ],
            ]);

        GeneralUtility::addInstance(ActiveExtensionConfigurationLoader::class, $loaderMock);

        $registrar = new RecordModuleRegistrar();
        $result = $registrar->buildModuleRegistrations();

        $this->assertArrayHasKey('theme_recordmodule_sys_category', $result);
        $this->assertArrayHasKey('theme_records', $result);

        $module = $result['theme_recordmodule_sys_category'];
        $this->assertSame('theme_records', $module['parent']);
        $this->assertSame(10, $module['sorting']);
        $this->assertSame([1, 2], $module['moduleData']['pids']);
        $this->assertSame('Categories', $module['labels']['title']);

        unset($GLOBALS['TCA']['sys_category']);
    }

    public function testBuildModuleRegistrationsSkipsUnknownTables(): void
    {
        $loaderMock = $this->createMock(ActiveExtensionConfigurationLoader::class);
        $loaderMock->method('getMergedConfigurationByFilename')
            ->with('RecordModules')
            ->willReturn([
                'nonexistent_table' => [
                    'pids' => '1',
                ],
            ]);

        GeneralUtility::addInstance(ActiveExtensionConfigurationLoader::class, $loaderMock);

        $registrar = new RecordModuleRegistrar();
        $result = $registrar->buildModuleRegistrations();

        $this->assertSame([], $result);
    }

    public function testClassCanBeInstantiated(): void
    {
        $registrar = new RecordModuleRegistrar();

        $this->assertInstanceOf(RecordModuleRegistrar::class, $registrar);
    }
}
