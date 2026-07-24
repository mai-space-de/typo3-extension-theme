<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\DataProcessing;

use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Page\ContentAreaCollection;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Sets a boolean template variable when a Hero CE is present on the page
 * (typically main content colPos 0 or beforeContent colPos 3).
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
        $currentPageUid = $cObj->getRequest()->getAttribute('frontend.page.information')?->getId() ?? 0;

        $processedData[$as] = $this->hasHero($processedData[$contentSource] ?? null, $currentPageUid);

        return $processedData;
    }

    /**
     * Only counts a Hero CE that actually lives on the current page. The
     * "beforeContent" column (colPos 3) is backend-layout-configured to
     * slide up the rootline when empty, so page-content data processing
     * (unlike the styles.content.get render column) resolves an ancestor's
     * Hero CE for every descendant page — without the pid check below,
     * {hero} is true sitewide and the page-level H1 in PageHeading.html
     * never renders on any page but the one that owns the Hero CE.
     */
    private function hasHero(mixed $content, int $currentPageUid): bool
    {
        if (!$content instanceof ContentAreaCollection) {
            return false;
        }

        foreach ($content as $area) {
            foreach ($area->getRecords() as $record) {
                if (!$record instanceof RecordInterface) {
                    continue;
                }

                if ($record->getMainType() !== 'tt_content') {
                    continue;
                }

                if ($record->getPid() !== $currentPageUid) {
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
