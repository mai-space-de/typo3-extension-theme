<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\DataProcessing;

use Maispace\MaiTheme\DataProcessing\HeroContentProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Page\ContentArea;
use TYPO3\CMS\Core\Page\ContentAreaCollection;
use TYPO3\CMS\Core\Page\ContentSlideMode;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class HeroContentProcessorTest extends TestCase
{
    private HeroContentProcessor $processor;

    private ContentObjectRenderer&MockObject $cObj;

    protected function setUp(): void
    {
        $this->processor = new HeroContentProcessor();
        $this->cObj = $this->createMock(ContentObjectRenderer::class);
    }

    public function testProcessorSetsHeroFalseWhenContentSourceMissing(): void
    {
        $result = $this->process([]);

        self::assertArrayHasKey('hero', $result);
        self::assertFalse($result['hero']);
    }

    public function testProcessorSetsHeroTrueWhenBeforeContentContainsHero(): void
    {
        $content = $this->createContentCollection([
            0 => [$this->createContentRecord('maispace_text')],
            3 => [$this->createContentRecord('maispace_hero')],
        ]);

        $result = $this->process(['content' => $content]);

        self::assertTrue($result['hero']);
    }

    public function testProcessorSetsHeroTrueWhenMainContentContainsHero(): void
    {
        $content = $this->createContentCollection([
            0 => [
                $this->createContentRecord('maispace_text'),
                $this->createContentRecord('maispace_hero'),
            ],
        ]);

        $result = $this->process(['content' => $content]);

        self::assertTrue($result['hero']);
    }

    public function testProcessorUsesCustomVariableNames(): void
    {
        $content = $this->createContentCollection([
            0 => [$this->createContentRecord('maispace_hero')],
        ]);

        $this->cObj
            ->method('stdWrapValue')
            ->willReturnCallback(
                static fn (string $key, array $config, mixed $default): mixed => match ($key) {
                    'as' => 'hasHero',
                    'contentSource' => 'pageContent',
                    default => $default,
                },
            );

        $result = $this->processor->process(
            $this->cObj,
            [],
            [
                'as' => 'hasHero',
                'contentSource' => 'pageContent',
            ],
            [
                'pageContent' => $content,
                'existingKey' => 'value',
            ],
        );

        self::assertSame('value', $result['existingKey']);
        self::assertTrue($result['hasHero']);
        self::assertArrayNotHasKey('hero', $result);
    }

    /**
     * @param array<int, list<RecordInterface>> $recordsByColPos
     */
    private function createContentCollection(array $recordsByColPos): ContentAreaCollection
    {
        $areas = [];
        foreach ($recordsByColPos as $colPos => $records) {
            $identifier = match ($colPos) {
                0 => 'content',
                3 => 'beforeContent',
                default => 'colPos' . $colPos,
            };

            $areas[$identifier] = new ContentArea(
                $identifier,
                $identifier,
                $colPos,
                ContentSlideMode::None,
                [],
                [],
                [],
                $records,
            );
        }

        return new ContentAreaCollection($areas);
    }

    private function createContentRecord(string $cType): RecordInterface&MockObject
    {
        $record = $this->createMock(RecordInterface::class);
        $record->method('getMainType')->willReturn('tt_content');
        $record->method('getRecordType')->willReturn($cType);

        return $record;
    }

    /**
     * @param array<string, mixed> $processedData
     * @return array<string, mixed>
     */
    private function process(array $processedData, array $processorConfiguration = []): array
    {
        $as = $processorConfiguration['as'] ?? 'hero';
        $contentSource = $processorConfiguration['contentSource'] ?? 'content';

        $this->cObj
            ->method('stdWrapValue')
            ->willReturnCallback(
                static function (string $key, array $config, mixed $default) use ($as, $contentSource): mixed {
                    return match ($key) {
                        'as' => $config['as'] ?? $as,
                        'contentSource' => $config['contentSource'] ?? $contentSource,
                        default => $default,
                    };
                },
            );

        return $this->processor->process(
            $this->cObj,
            [],
            $processorConfiguration,
            $processedData,
        );
    }
}
