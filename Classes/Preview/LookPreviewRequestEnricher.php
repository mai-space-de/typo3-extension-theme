<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Preview;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3\CMS\Frontend\Page\PageParts;

/**
 * Ergänzt den Look-Vorschaurequest um das, was eine echte Frontend-Anfrage
 * schon mitbringt. News liest die Seiten-ID aus dem Routing, Veranstaltungen
 * registrieren sich als nicht cachebares Plugin über die Page-Parts.
 */
final class LookPreviewRequestEnricher
{
    public const ATTRIBUTE = 'mai.lookPreview';

    public function enrich(ServerRequestInterface $request): ServerRequestInterface
    {
        if (!$this->isLookPreview($request)) {
            return $request;
        }

        $pageInformation = $request->getAttribute('frontend.page.information');
        $enriched = $request->withAttribute(self::ATTRIBUTE, true);
        if (!$request->getAttribute('routing') instanceof PageArguments && $pageInformation instanceof PageInformation) {
            $enriched = $enriched->withAttribute(
                'routing',
                new PageArguments($pageInformation->getId(), '0', []),
            );
        }
        if (!$request->getAttribute('frontend.page.parts') instanceof PageParts) {
            $enriched = $enriched->withAttribute('frontend.page.parts', new PageParts());
        }

        return $enriched;
    }

    public function isLookPreview(ServerRequestInterface $request): bool
    {
        if ($request->getAttribute(self::ATTRIBUTE) === true) {
            return true;
        }

        return $request->getAttribute('frontend.page.information') instanceof PageInformation
            && !$request->getAttribute('routing') instanceof PageArguments
            && !$request->getAttribute('frontend.page.parts') instanceof PageParts;
    }
}
