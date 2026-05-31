<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\DataProcessing;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * @example TypoScript:
 * 30 = Maispace\MaiTheme\DataProcessing\LanguageMenuProcessor
 * 30 {
 *     as = languages
 * }
 */
final class LanguageMenuProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $as = $cObj->stdWrapValue('as', $processorConfiguration, 'languages');

        $request = $cObj->getRequest();
        $site = $request->getAttribute('site');

        if (!$site instanceof Site) {
            return $processedData;
        }

        $currentLanguage = $request->getAttribute('language');
        $currentLanguageId = $currentLanguage instanceof SiteLanguage
            ? $currentLanguage->getLanguageId()
            : 0;

        $languages = $this->buildLanguageMenu($site, $request, $currentLanguageId);
        $processedData[$as] = $languages;

        return $processedData;
    }

    private function buildLanguageMenu(
        Site $site,
        ServerRequestInterface $request,
        int $currentLanguageId,
    ): array {
        $pageInformation = $request->getAttribute('frontend.page.information');
        $pageId = $pageInformation?->getId();

        $items = [];
        foreach ($site->getLanguages() as $language) {
            if (!$language->isEnabled()) {
                continue;
            }

            $languageId = $language->getLanguageId();

            $url = $this->generateLanguageUrl($site, $pageId, $language);

            $label = $language->getNavigationTitle() ?: $language->getTitle();
            $hreflang = $language->getHreflang() ?: $language->getLocale()->getName();

            $items[] = [
                'label' => $label,
                'url' => $url,
                'isCurrent' => $languageId === $currentLanguageId,
                'languageUid' => $languageId,
                'hreflang' => $hreflang,
                'locale' => $language->getLocale()->getName(),
                'navigationTitle' => $language->getNavigationTitle(),
                'title' => $language->getTitle(),
                'flag' => $language->getFlagIdentifier(),
                'direction' => $language->getLocale()->isRightToLeftLanguageDirection() ? 'rtl' : 'ltr',
            ];
        }

        return $items;
    }

    private function generateLanguageUrl(
        Site $site,
        ?int $pageId,
        SiteLanguage $language,
    ): string {
        if ($pageId === null) {
            return (string) $language->getBase();
        }

        try {
            $uri = $site->getRouter()->generateUri(
                $pageId,
                ['_language' => $language],
            );
            return (string) $uri;
        } catch (\Exception) {
            return (string) $language->getBase();
        }
    }
}
