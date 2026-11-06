<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Preview;

use Maispace\MaiTheme\Preview\LookPreviewRequestEnricher;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Frontend\Page\PageInformation;
use TYPO3\CMS\Frontend\Page\PageParts;

final class LookPreviewRequestEnricherTest extends TestCase
{
    public function testFrontendRequestStaysUntouched(): void
    {
        $pageInformation = new PageInformation();
        $pageInformation->setId(1);
        $request = (new ServerRequest('https://example.test/'))
            ->withAttribute('frontend.page.information', $pageInformation)
            ->withAttribute('routing', new PageArguments(1, '0', []))
            ->withAttribute('frontend.page.parts', new PageParts());

        $enriched = (new LookPreviewRequestEnricher())->enrich($request);

        self::assertSame($request, $enriched);
    }

    public function testLookPreviewGetsPageIdAndPageParts(): void
    {
        $pageInformation = new PageInformation();
        $pageInformation->setId(42);
        $request = (new ServerRequest('https://example.test/typo3/look/preview'))
            ->withAttribute('frontend.page.information', $pageInformation)
            ->withAttribute('routing', new \stdClass());

        $enriched = (new LookPreviewRequestEnricher())->enrich($request);

        $routing = $enriched->getAttribute('routing');
        self::assertInstanceOf(PageArguments::class, $routing);
        self::assertSame(42, $routing->getPageId());
        self::assertInstanceOf(PageParts::class, $enriched->getAttribute('frontend.page.parts'));
        self::assertTrue($enriched->getAttribute(LookPreviewRequestEnricher::ATTRIBUTE));
    }
}
