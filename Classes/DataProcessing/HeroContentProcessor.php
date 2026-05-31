<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\DataProcessing;

use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Page\ContentAreaCollection;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Sets a boolean template variable when colPos 0 contains a Hero content element.
 *
 * @example TypoScript:
 * 6 = Maispace\MaiTheme\DataProcessing\HeroContentProcessor
 * 6 {
 *     as = hero
 *     contentSource = content
 * }
 */
final class HeroContentProcessor implements DataProcessorInterface
{
    private const HERO_CTYPE = 'maispace_hero';

    private const MAIN_CONTENT_COL_POS = 0;

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $as = $cObj->stdWrapValue('as', $processorConfiguration, 'hero');
        $contentSource = $cObj->stdWrapValue('contentSource', $processorConfiguration, 'content');

        $processedData[$as] = $this->hasHeroInMainContent($processedData[$contentSource] ?? null);

        return $processedData;
    }

    private function hasHeroInMainContent(mixed $content): bool
    {
        if (!$content instanceof ContentAreaCollection) {
            return false;
        }

        foreach ($content as $area) {
            if ($area->getColPos() !== self::MAIN_CONTENT_COL_POS) {
                continue;
            }

            foreach ($area->getRecords() as $record) {
                if (!$record instanceof RecordInterface) {
                    continue;
                }

                if ($record->getMainType() !== 'tt_content') {
                    continue;
                }

                if ($record->getRecordType() === self::HERO_CTYPE) {
                    return true;
                }
            }
        }

        return false;
    }
}
