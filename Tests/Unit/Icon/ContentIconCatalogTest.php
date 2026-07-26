<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Icon;

use Maispace\MaiTheme\Icon\ContentIconCatalog;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

final class ContentIconCatalogTest extends TestCase
{
    #[Test]
    public function catalogContainsSeededFeatureboxIcons(): void
    {
        self::assertArrayHasKey('map-pin', ContentIconCatalog::ICONS);
        self::assertArrayHasKey('hand-heart', ContentIconCatalog::ICONS);
        self::assertArrayHasKey('user', ContentIconCatalog::ICONS);
        self::assertArrayHasKey('user-circle', ContentIconCatalog::ICONS);
    }

    #[Test]
    public function tcaItemsIncludeEmptyOptionAndIcons(): void
    {
        $items = ContentIconCatalog::tcaItems();

        self::assertSame('', $items[0]['value']);
        self::assertGreaterThan(1, count($items));

        $mapPin = null;
        foreach ($items as $item) {
            if (($item['value'] ?? null) === 'map-pin') {
                $mapPin = $item;
                break;
            }
        }

        self::assertNotNull($mapPin);
        self::assertSame('mai-theme-content-icon-map-pin', $mapPin['icon']);
        self::assertStringContainsString('icon.map_pin', $mapPin['label']);
    }

    #[Test]
    public function iconRegistryEntriesPointAtSvgSources(): void
    {
        $entries = ContentIconCatalog::iconRegistryEntries();

        self::assertArrayHasKey('mai-theme-content-icon-map-pin', $entries);
        self::assertSame(SvgIconProvider::class, $entries['mai-theme-content-icon-map-pin']['provider']);
        self::assertSame(
            'EXT:mai_theme/Resources/Public/Icons/Content/map-pin.svg',
            $entries['mai-theme-content-icon-map-pin']['source'],
        );
        self::assertCount(count(ContentIconCatalog::ICONS), $entries);
    }

    #[Test]
    public function svgFilesExistOnDisk(): void
    {
        $base = dirname(__DIR__, 3) . '/Resources/Public/Icons/Content/';

        foreach (array_keys(ContentIconCatalog::ICONS) as $identifier) {
            $path = $base . $identifier . '.svg';
            self::assertFileExists($path, 'Missing icon SVG: ' . $identifier);
            self::assertStringContainsString('<svg', (string) file_get_contents($path));
        }
    }
}
