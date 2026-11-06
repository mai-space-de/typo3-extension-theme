<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\EventListener;

use Maispace\MaiTheme\Preview\LookPreviewRequestEnricher;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\Event\AfterContentObjectRendererInitializedEvent;

/**
 * Look rendert Inhaltselemente ohne den Frontend-Middleware-Lauf. Nicht
 * cachebare Plugins (Veranstaltungen) würden sonst nur einen Platzhalter
 * anmelden und daran scheitern, dass die Page-Parts fehlen. Hier rendern
 * sie direkt, und die Seiten-ID steht für Turbo (News) bereit.
 */
#[AsEventListener(identifier: 'mai-theme/look-preview-runtime')]
final readonly class LookPreviewRuntimeListener
{
    public function __construct(
        private LookPreviewRequestEnricher $lookPreviewRequestEnricher,
    ) {}

    public function __invoke(AfterContentObjectRendererInitializedEvent $event): void
    {
        $contentObject = $event->getContentObjectRenderer();
        $request = $contentObject->getRequest();
        if (!$this->lookPreviewRequestEnricher->isLookPreview($request)) {
            return;
        }

        $enriched = $this->lookPreviewRequestEnricher->enrich($request);
        if ($enriched !== $request) {
            $contentObject->setRequest($enriched);
            $GLOBALS['TYPO3_REQUEST'] = $enriched;
        }
        if ($contentObject->getUserObjectType() === false) {
            $contentObject->setUserObjectType(ContentObjectRenderer::OBJECTTYPE_USER_INT);
        }
    }
}
