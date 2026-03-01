<?php

declare(strict_types = 1);

namespace Maispace\Theme\Tests\Functional\Frontend;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

/**
 * Integration tests for the frontend page rendering pipeline.
 *
 * Each test boots a real TYPO3 instance backed by an in-process SQLite
 * database, imports a root-page fixture, wires the extension TypoScript, and
 * then fires an InternalRequest — exactly as the TYPO3 frontend stack would
 * handle a real browser request.
 *
 * This verifies end-to-end behaviour that no amount of mocking can provide:
 *   • HTTP response status codes
 *   • Presence of the stylesheet <link> tag injected by the asset collector
 *   • HTML structural invariants (DOCTYPE, skip-link, main#main-content,
 *     <header>, <footer>) that would silently break if a Fluid tag disappeared
 */
final class PageRenderingTest extends FunctionalTestCase
{
    /** Load maispace/assets (provides the <mai:scss> ViewHelper used in the layout). */
    protected array $testExtensionsToLoad = [
        'maispace/assets',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');

        $this->writeSiteConfiguration(
            'default',
            $this->buildSiteConfiguration(1, 'https://example.com/')
        );

        $this->setUpFrontendRootPage(
            1,
            ['EXT:theme/Configuration/TypoScript/setup.typoscript']
        );
    }

    // ── HTTP status codes ──────────────────────────────────────────────────────

    public function testRootPageReturnsHttp200(): void
    {
        $response = $this->executeFrontendSubRequest(
            new InternalRequest('https://example.com/')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    public function testNonExistentPageReturnsHttp404(): void
    {
        $response = $this->executeFrontendSubRequest(
            new InternalRequest('https://example.com/this-page-does-not-exist/')
        );

        self::assertSame(404, $response->getStatusCode());
    }

    // ── HTML document structure ────────────────────────────────────────────────

    public function testRenderedHtmlContainsDoctype(): void
    {
        $body = $this->fetchRootPageBody();

        self::assertStringContainsString('<!DOCTYPE html>', $body);
    }

    public function testRenderedHtmlContainsMainContentAnchor(): void
    {
        $body = $this->fetchRootPageBody();

        self::assertStringContainsString('id="main-content"', $body);
    }

    public function testRenderedHtmlContainsSkipToContentLink(): void
    {
        $body = $this->fetchRootPageBody();

        self::assertStringContainsString('href="#main-content"', $body);
    }

    public function testRenderedHtmlContainsHeaderElement(): void
    {
        $body = $this->fetchRootPageBody();

        self::assertStringContainsString('<header', $body);
    }

    public function testRenderedHtmlContainsFooterElement(): void
    {
        $body = $this->fetchRootPageBody();

        self::assertStringContainsString('<footer', $body);
    }

    // ── Asset injection ────────────────────────────────────────────────────────

    public function testRenderedHtmlContainsStylesheetLinkTag(): void
    {
        $body = $this->fetchRootPageBody();

        self::assertMatchesRegularExpression(
            '/<link[^>]+rel=["\']stylesheet["\']/',
            $body,
            'A <link rel="stylesheet"> tag must be present in the rendered page'
        );
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function fetchRootPageBody(): string
    {
        $response = $this->executeFrontendSubRequest(
            new InternalRequest('https://example.com/')
        );

        return (string)$response->getBody();
    }
}
