<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\DataProcessing;

use Maispace\MaiTheme\DataProcessing\LanguageMenuProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Routing\RouterInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class LanguageMenuProcessorTest extends TestCase
{
    private LanguageMenuProcessor $processor;
    private ContentObjectRenderer&MockObject $cObj;
    private ServerRequestInterface&MockObject $request;
    private Site&MockObject $site;

    /** @var array<string, mixed> */
    private array $requestAttributes = [];

    protected function setUp(): void
    {
        $this->processor = new LanguageMenuProcessor();
        $this->cObj = $this->createMock(ContentObjectRenderer::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->site = $this->createMock(Site::class);
        $this->requestAttributes = [];

        $this->cObj->method('getRequest')->willReturn($this->request);
        $this->request
            ->method('getAttribute')
            ->willReturnCallback(function (string $name, mixed $default = null): mixed {
                return $this->requestAttributes[$name] ?? $default;
            });
    }

    public function testProcessorReturnsProcessedDataWhenNoSite(): void
    {
        $requestWithoutSite = $this->createMock(ServerRequestInterface::class);
        $requestWithoutSite->method('getAttribute')->willReturn(null);

        $cObj = $this->createMock(ContentObjectRenderer::class);
        $cObj->method('getRequest')->willReturn($requestWithoutSite);

        $result = $this->processor->process(
            $cObj,
            [],
            [],
            ['existingKey' => 'value'],
        );

        self::assertSame(['existingKey' => 'value'], $result);
    }

    public function testProcessorReturnsEmptyArrayForNoLanguages(): void
    {
        $this->requestAttributes['site'] = $this->site;
        $this->setupSiteLanguages([]);

        $result = $this->process([]);

        self::assertArrayHasKey('languages', $result);
        self::assertEmpty($result['languages']);
    }

    public function testProcessorReturnsLanguagesWithCorrectKeys(): void
    {
        $deLanguage = $this->createSiteLanguage(0, 'de', 'Deutsch', 'de-DE', 'de-DE');
        $enLanguage = $this->createSiteLanguage(1, 'en', 'English', 'en-GB', 'en-GB');

        $this->requestAttributes['site'] = $this->site;
        $this->requestAttributes['language'] = $deLanguage;
        $this->setupSiteLanguages([$deLanguage, $enLanguage]);
        $this->setupRouter('/de/', '/en/');
        $this->setupPageInformation(42);

        $result = $this->process([]);

        self::assertArrayHasKey('languages', $result);
        $languages = $result['languages'];
        self::assertCount(2, $languages);

        self::assertSame('Deutsch', $languages[0]['label']);
        self::assertSame('/de/', $languages[0]['url']);
        self::assertTrue($languages[0]['isCurrent']);
        self::assertSame(0, $languages[0]['languageUid']);
        self::assertSame('de-DE', $languages[0]['hreflang']);
        self::assertSame('de-DE', $languages[0]['locale']);
        self::assertSame('Deutsch', $languages[0]['navigationTitle']);
        self::assertSame('Deutsch', $languages[0]['title']);
        self::assertSame('de', $languages[0]['flag']);
        self::assertSame('ltr', $languages[0]['direction']);

        self::assertSame('English', $languages[1]['label']);
        self::assertSame('/en/', $languages[1]['url']);
        self::assertFalse($languages[1]['isCurrent']);
        self::assertSame(1, $languages[1]['languageUid']);
        self::assertSame('en-GB', $languages[1]['hreflang']);
        self::assertSame('en-GB', $languages[1]['locale']);
        self::assertSame('ltr', $languages[1]['direction']);
    }

    public function testProcessorUsesCustomOutputKey(): void
    {
        $deLanguage = $this->createSiteLanguage(0, 'de', 'Deutsch', 'de-DE', 'de');
        $this->requestAttributes['site'] = $this->site;
        $this->requestAttributes['language'] = $deLanguage;
        $this->setupSiteLanguages([$deLanguage]);
        $this->setupRouter('/de/');
        $this->setupPageInformation(42);

        $this->cObj
            ->expects(self::once())
            ->method('stdWrapValue')
            ->with('as', ['as' => 'myLang'], 'languages')
            ->willReturn('myLang');

        $result = $this->processor->process(
            $this->cObj,
            [],
            ['as' => 'myLang'],
            [],
        );

        self::assertArrayHasKey('myLang', $result);
        self::assertArrayNotHasKey('languages', $result);
    }

    public function testProcessorFallsBackToLanguageBaseOnRouterException(): void
    {
        $deLanguage = $this->createSiteLanguage(0, 'de', 'Deutsch', 'de-DE', 'de');
        $enLanguage = $this->createSiteLanguage(1, 'en', 'English', 'en-GB', 'en');

        $router = $this->createMock(RouterInterface::class);
        $router
            ->expects(self::any())
            ->method('generateUri')
            ->willThrowException(new \RuntimeException('Router failure'));

        $this->site->method('getRouter')->willReturn($router);

        $this->requestAttributes['site'] = $this->site;
        $this->setupSiteLanguages([$deLanguage, $enLanguage]);

        $result = $this->process([]);

        $languages = $result['languages'];
        self::assertSame('Deutsch', $languages[0]['label']);
        self::assertSame('https://example.com/de-DE/', $languages[0]['url']);
    }

    public function testProcessorHandlesMissingPageInformation(): void
    {
        $deLanguage = $this->createSiteLanguage(0, 'de', 'Deutsch', 'de-DE', 'de');
        $enLanguage = $this->createSiteLanguage(1, 'en', 'English', 'en-GB', 'en');

        $this->requestAttributes['site'] = $this->site;
        $this->requestAttributes['language'] = $deLanguage;
        $this->setupSiteLanguages([$deLanguage, $enLanguage]);

        $result = $this->process([]);

        $languages = $result['languages'];
        self::assertCount(2, $languages);
        self::assertStringContainsString('example.com/de-DE/', $languages[0]['url']);
    }

    public function testProcessorHandlesRtlLanguageDirection(): void
    {
        $arLanguage = $this->createSiteLanguage(3, 'ar', 'العربية', 'ar-SA', 'ar', true);
        $this->requestAttributes['site'] = $this->site;
        $this->requestAttributes['language'] = $arLanguage;
        $this->setupSiteLanguages([$arLanguage]);
        $this->setupRouter('/ar/');
        $this->setupPageInformation(42);

        $result = $this->process([]);

        self::assertSame('rtl', $result['languages'][0]['direction']);
    }

    public function testProcessorGeneratesAllFourSiteLanguages(): void
    {
        $deLanguage = $this->createSiteLanguage(0, 'de', 'Deutsch', 'de-DE', 'de-DE');
        $enLanguage = $this->createSiteLanguage(1, 'en-us-gb', 'English', 'en-US', 'en-GB');
        $ukLanguage = $this->createSiteLanguage(2, 'ua', 'Українська', 'uk_UA', 'uk-UA');
        $arLanguage = $this->createSiteLanguage(3, 'sa', 'العربية', 'ar_SA', 'ar-SA', true);

        $this->requestAttributes['site'] = $this->site;
        $this->requestAttributes['language'] = $deLanguage;
        $this->setupSiteLanguages([$deLanguage, $enLanguage, $ukLanguage, $arLanguage]);
        $this->setupRouter('/', '/en/', '/ua/', '/ar/');
        $this->setupPageInformation(1);

        $result = $this->process([]);

        $languages = $result['languages'];
        self::assertCount(4, $languages);
        self::assertSame(0, $languages[0]['languageUid']);
        self::assertTrue($languages[0]['isCurrent']);
        self::assertSame('Deutsch', $languages[0]['label']);
        self::assertSame(1, $languages[1]['languageUid']);
        self::assertSame('English', $languages[1]['label']);
        self::assertSame('en-GB', $languages[1]['hreflang']);
        self::assertSame(2, $languages[2]['languageUid']);
        self::assertSame('Українська', $languages[2]['label']);
        self::assertSame('uk-UA', $languages[2]['hreflang']);
        self::assertSame(3, $languages[3]['languageUid']);
        self::assertSame('العربية', $languages[3]['label']);
        self::assertSame('rtl', $languages[3]['direction']);
        self::assertSame('ar-SA', $languages[3]['hreflang']);
    }

    public function testProcessorFallsBackToLocaleWhenHreflangEmpty(): void
    {
        $enLanguage = $this->createSiteLanguage(1, 'en-us-gb', 'English', 'en-US', '');

        $this->requestAttributes['site'] = $this->site;
        $this->requestAttributes['language'] = $enLanguage;
        $this->setupSiteLanguages([$enLanguage]);
        $this->setupRouter('/en/');
        $this->setupPageInformation(42);

        $result = $this->process([]);

        self::assertSame('en-US', $result['languages'][0]['hreflang']);
    }

    public function testProcessorExcludesDisabledLanguages(): void
    {
        $deLanguage = $this->createSiteLanguage(0, 'de', 'Deutsch', 'de-DE', 'de');
        $enLanguage = $this->createMock(SiteLanguage::class);
        $enLanguage->method('getLanguageId')->willReturn(1);
        $enLanguage->method('isEnabled')->willReturn(false);
        $enLanguage->method('getBase')->willReturn(
            new \TYPO3\CMS\Core\Http\Uri('https://example.com/en/'),
        );

        $this->requestAttributes['site'] = $this->site;
        $this->requestAttributes['language'] = $deLanguage;
        $this->setupSiteLanguages([$deLanguage, $enLanguage]);
        $this->setupRouter('/de/');
        $this->setupPageInformation(42);

        $result = $this->process([]);

        self::assertCount(1, $result['languages']);
        self::assertSame(0, $result['languages'][0]['languageUid']);
    }

    private function process(array $processorConfiguration): array
    {
        $as = $processorConfiguration['as'] ?? 'languages';

        $this->cObj
            ->method('stdWrapValue')
            ->with('as', self::anything(), 'languages')
            ->willReturn($as);

        return $this->processor->process(
            $this->cObj,
            [],
            $processorConfiguration,
            [],
        );
    }

    private function createSiteLanguage(
        int $languageId,
        string $flag,
        string $title,
        string $locale,
        string $hreflang,
        bool $rtl = false,
    ): SiteLanguage {
        $base = new \TYPO3\CMS\Core\Http\Uri('https://example.com/' . $locale . '/');

        $localeObj = $this->createMock(\TYPO3\CMS\Core\Localization\Locale::class);
        $localeObj->method('getName')->willReturn($locale);
        $localeObj->method('isRightToLeftLanguageDirection')->willReturn($rtl);

        $language = $this->createMock(SiteLanguage::class);
        $language->method('getLanguageId')->willReturn($languageId);
        $language->method('getTitle')->willReturn($title);
        $language->method('getNavigationTitle')->willReturn($title);
        $language->method('getFlagIdentifier')->willReturn($flag);
        $language->method('getHreflang')->willReturn($hreflang);
        $language->method('getBase')->willReturn($base);
        $language->method('getLocale')->willReturn($localeObj);
        $language->method('isEnabled')->willReturn(true);

        return $language;
    }

    private function setupSiteLanguages(array $languages): void
    {
        $this->site->method('getLanguages')->willReturn($languages);
    }

    private function setupRouter(string ...$urls): void
    {
        $router = $this->createMock(RouterInterface::class);
        $callIndex = 0;
        $router
            ->expects(self::any())
            ->method('generateUri')
            ->willReturnCallback(function () use ($urls, &$callIndex) {
                $url = $urls[$callIndex] ?? '/';
                $callIndex++;
                return new \TYPO3\CMS\Core\Http\Uri($url);
            });

        $this->site->method('getRouter')->willReturn($router);
    }

    private function setupPageInformation(int $pageId): void
    {
        $pageInfo = new class ($pageId) {
            public function __construct(private readonly int $pageId) {}
            public function getId(): int
            {
                return $this->pageId;
            }
        };

        $this->requestAttributes['frontend.page.information'] = $pageInfo;
    }
}
