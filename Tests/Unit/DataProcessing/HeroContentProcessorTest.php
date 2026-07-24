<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\DataProcessing;

use Maispace\MaiTheme\DataProcessing\HeroContentProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Page\ContentArea;
use TYPO3\CMS\Core\Page\ContentAreaCollection;
use TYPO3\CMS\Core\Page\ContentSlideMode;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Page\PageInformation;

final class HeroContentProcessorTest extends TestCase
{
    private const CURRENT_PAGE_UID = 3;

    private HeroContentProcessor $processor;

    private ContentObjectRenderer&MockObject $cObj;

    protected function setUp(): void
    {
        $this->processor = new HeroContentProcessor();
        $this->cObj = $this->createMock(ContentObjectRenderer::class);

        // PageInformation is a final DTO — real instance instead of a mock.
        $pageInformation = new PageInformation();
        $pageInformation->setId(self::CURRENT_PAGE_UID);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttribute')
            ->with('frontend.page.information')
            ->willReturn($pageInformation);

        $this->cObj->method('getRequest')->willReturn($request);
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

    /**
     * The "beforeContent" column (colPos 3) is backend-layout-configured to
     * slide up the rootline when empty, so page-content data processing can
     * resolve an ancestor page's Hero CE even though it is not rendered on
     * the current page. Without a pid check, {hero} would be true on every
     * descendant page and the page-level H1 in PageHeading.html would never
     * render anywhere but the page that owns the Hero CE.
     */
    public function testProcessorSetsHeroFalseWhenHeroBelongsToAnAncestorPage(): void
    {
        $content = $this->createContentCollection([
            3 => [$this->createContentRecord('maispace_hero', pid: 1)],
        ]);

        $result = $this->process(['content' => $content]);

        self::assertFalse($result['hero']);
    }

    public function testProcessorUsesCustomVariableNames(): void
    {
        $content = $this->createContentCollection([
            0 => [$this->createContentRecord('maispace_hero')],
        ]);

        $this->cObj
            ->method('stdWrapValue')
            ->willReturnCallback(
                static fn(string $key, array $config, mixed $default): mixed => match ($key) {
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

    private function createContentRecord(string $cType, int $pid = self::CURRENT_PAGE_UID): RecordInterface&MockObject
    {
        $record = $this->createMock(RecordInterface::class);
        $record->method('getMainType')->willReturn('tt_content');
        $record->method('getRecordType')->willReturn($cType);
        $record->method('getPid')->willReturn($pid);

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
