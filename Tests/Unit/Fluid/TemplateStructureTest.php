<?php

declare(strict_types=1);

namespace Maispace\Theme\Tests\Unit\Fluid;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the structural integrity of Fluid templates without requiring a
 * running TYPO3 instance. Catches missing or malformed HTML tags, absent
 * accessibility attributes, and broken Fluid section/partial wiring that
 * would cause silent rendering failures in the browser.
 */
class TemplateStructureTest extends TestCase
{
    private string $resourcesPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resourcesPath = dirname(__DIR__, 3) . '/Resources/Private';
    }

    private function readTemplate(string $relativePath): string
    {
        $fullPath = $this->resourcesPath . '/' . $relativePath;
        $this->assertFileExists($fullPath, "Template file '{$relativePath}' does not exist.");
        $content = file_get_contents($fullPath);
        $this->assertNotFalse($content, "Could not read template file '{$relativePath}'.");
        return $content;
    }

    // ── Layout: Page/Default ───────────────────────────────────────────────────

    public function testDefaultLayoutHasDoctype(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('<!DOCTYPE html>', $html);
    }

    public function testDefaultLayoutHasHtmlElement(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('<html', $html);
        $this->assertStringContainsString('</html>', $html);
    }

    public function testDefaultLayoutHasLangAttributeOnHtml(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertMatchesRegularExpression('/<html[^>]+lang=/i', $html);
    }

    public function testDefaultLayoutHasHeadSection(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('<head>', $html);
        $this->assertStringContainsString('</head>', $html);
    }

    public function testDefaultLayoutHasMetaCharset(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('charset="UTF-8"', $html);
    }

    public function testDefaultLayoutHasMetaViewport(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('name="viewport"', $html);
    }

    public function testDefaultLayoutHasTitleSection(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString('</title>', $html);
        // Fluid section rendered into <title>
        $this->assertStringContainsString('f:render', $html);
        $this->assertStringContainsString('section="Title"', $html);
    }

    public function testDefaultLayoutHasBodyElement(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('<body>', $html);
        $this->assertStringContainsString('</body>', $html);
    }

    public function testDefaultLayoutRendersBodySection(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('section="Body"', $html);
    }

    public function testDefaultLayoutHasSkipLink(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('skip-link', $html);
        $this->assertStringContainsString('#main-content', $html);
    }

    public function testDefaultLayoutIncludesScssViewHelper(): void
    {
        $html = $this->readTemplate('Layouts/Page/Default.html');
        $this->assertStringContainsString('mai:scss', $html);
        $this->assertStringContainsString('bundle.scss', $html);
    }

    // ── Template: Page/Default ─────────────────────────────────────────────────

    public function testDefaultTemplateExtendsLayout(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        $this->assertStringContainsString('f:layout', $html);
        $this->assertStringContainsString('Page/Default', $html);
    }

    public function testDefaultTemplateDefinesTitleSection(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        $this->assertStringContainsString('f:section', $html);
        $this->assertStringContainsString('name="Title"', $html);
    }

    public function testDefaultTemplateTitleFallsBackToPageTitle(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        // seo_title check with fallback to data.title
        $this->assertStringContainsString('data.seo_title', $html);
        $this->assertStringContainsString('data.title', $html);
        $this->assertStringContainsString('settings.site.name', $html);
    }

    public function testDefaultTemplateHasMainElement(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        $this->assertStringContainsString('<main', $html);
        $this->assertStringContainsString('</main>', $html);
    }

    public function testDefaultTemplateMainHasIdForSkipLink(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        $this->assertStringContainsString('id="main-content"', $html);
    }

    public function testDefaultTemplateRendersHeaderPartial(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        $this->assertStringContainsString('partial="Page/Header"', $html);
    }

    public function testDefaultTemplateRendersFooterPartial(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        $this->assertStringContainsString('partial="Page/Footer"', $html);
    }

    public function testDefaultTemplatePassesDataAndSettingsToPartials(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        $this->assertStringContainsString('data: data', $html);
        $this->assertStringContainsString('settings: settings', $html);
    }

    public function testDefaultTemplateSidebarLayoutUsesAside(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        $this->assertStringContainsString('<aside', $html);
        $this->assertStringContainsString('sidebar', $html);
    }

    public function testDefaultTemplateRendersContentViaTypoScript(): void
    {
        $html = $this->readTemplate('Templates/Page/Default.html');
        $this->assertStringContainsString('lib.content', $html);
        $this->assertStringContainsString('f:cObject', $html);
    }

    // ── Partial: Page/Header ───────────────────────────────────────────────────

    public function testHeaderPartialHasHeaderElement(): void
    {
        $html = $this->readTemplate('Partials/Page/Header.html');
        $this->assertStringContainsString('<header', $html);
        $this->assertStringContainsString('</header>', $html);
    }

    public function testHeaderPartialHasRoleBanner(): void
    {
        $html = $this->readTemplate('Partials/Page/Header.html');
        $this->assertStringContainsString('role="banner"', $html);
    }

    public function testHeaderPartialHasLogoLink(): void
    {
        $html = $this->readTemplate('Partials/Page/Header.html');
        $this->assertStringContainsString('site-header-logo', $html);
        $this->assertStringContainsString('f:uri.page', $html);
    }

    public function testHeaderPartialLogoLinkHasAriaLabel(): void
    {
        $html = $this->readTemplate('Partials/Page/Header.html');
        // aria-label on the logo link for screen readers
        $this->assertStringContainsString('aria-label="{settings.site.name}"', $html);
    }

    public function testHeaderPartialHasNavElement(): void
    {
        $html = $this->readTemplate('Partials/Page/Header.html');
        $this->assertStringContainsString('<nav', $html);
        $this->assertStringContainsString('</nav>', $html);
    }

    public function testHeaderPartialNavHasAriaLabel(): void
    {
        $html = $this->readTemplate('Partials/Page/Header.html');
        // nav must carry an accessible label (from locallang or settings)
        $this->assertMatchesRegularExpression('/<nav[^>]+aria-label=/i', $html);
    }

    public function testHeaderPartialRendersNavigationPartial(): void
    {
        $html = $this->readTemplate('Partials/Page/Header.html');
        $this->assertStringContainsString('partial="Page/Navigation"', $html);
    }

    public function testHeaderPartialShowsActionsConditionally(): void
    {
        $html = $this->readTemplate('Partials/Page/Header.html');
        $this->assertStringContainsString('settings.header.showActions', $html);
    }

    // ── Partial: Page/Footer ───────────────────────────────────────────────────

    public function testFooterPartialHasFooterElement(): void
    {
        $html = $this->readTemplate('Partials/Page/Footer.html');
        $this->assertStringContainsString('<footer', $html);
        $this->assertStringContainsString('</footer>', $html);
    }

    public function testFooterPartialHasRoleContentinfo(): void
    {
        $html = $this->readTemplate('Partials/Page/Footer.html');
        $this->assertStringContainsString('role="contentinfo"', $html);
    }

    public function testFooterPartialHasCopyrightNotice(): void
    {
        $html = $this->readTemplate('Partials/Page/Footer.html');
        $this->assertStringContainsString('&copy;', $html);
        $this->assertStringContainsString('f:format.date', $html);
        $this->assertStringContainsString('settings.site.name', $html);
    }

    public function testFooterPartialShowsNavConditionally(): void
    {
        $html = $this->readTemplate('Partials/Page/Footer.html');
        $this->assertStringContainsString('settings.footer.showNav', $html);
    }

    public function testFooterPartialNavHasAriaLabel(): void
    {
        $html = $this->readTemplate('Partials/Page/Footer.html');
        $this->assertMatchesRegularExpression('/<nav[^>]+aria-label=/i', $html);
    }

    public function testFooterPartialShowsColumnsConditionally(): void
    {
        $html = $this->readTemplate('Partials/Page/Footer.html');
        $this->assertStringContainsString('settings.footer.columns', $html);
    }

    // ── Partial: Page/Navigation ───────────────────────────────────────────────

    public function testNavigationPartialHasUlElement(): void
    {
        $html = $this->readTemplate('Partials/Page/Navigation.html');
        $this->assertStringContainsString('<ul', $html);
        $this->assertStringContainsString('</ul>', $html);
    }

    public function testNavigationPartialUlHasRoleList(): void
    {
        $html = $this->readTemplate('Partials/Page/Navigation.html');
        $this->assertStringContainsString('role="list"', $html);
    }

    public function testNavigationPartialIteratesNavigationPages(): void
    {
        $html = $this->readTemplate('Partials/Page/Navigation.html');
        $this->assertStringContainsString('settings.navigationPages', $html);
        $this->assertStringContainsString('f:for', $html);
    }

    public function testNavigationPartialRendersAnchors(): void
    {
        $html = $this->readTemplate('Partials/Page/Navigation.html');
        $this->assertStringContainsString('<a', $html);
        $this->assertStringContainsString('f:uri.page', $html);
    }

    public function testNavigationPartialRendersItemTitle(): void
    {
        $html = $this->readTemplate('Partials/Page/Navigation.html');
        $this->assertStringContainsString('{item.title}', $html);
    }

    public function testNavigationPartialSetsAriaCurrentOnActivePage(): void
    {
        $html = $this->readTemplate('Partials/Page/Navigation.html');
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertStringContainsString('item.isActive', $html);
    }

    public function testNavigationPartialRendersChildItems(): void
    {
        $html = $this->readTemplate('Partials/Page/Navigation.html');
        $this->assertStringContainsString('item.children', $html);
        $this->assertStringContainsString('{child.title}', $html);
    }

    public function testNavigationPartialChildrenAriaCurrentIsSet(): void
    {
        $html = $this->readTemplate('Partials/Page/Navigation.html');
        $this->assertStringContainsString('child.isActive', $html);
    }

    // ── Cross-template wiring ──────────────────────────────────────────────────

    /**
     * @return array<string, array{string}>
     */
    public static function allTemplateFilesProvider(): array
    {
        return [
            'layout Default'     => ['Layouts/Page/Default.html'],
            'template Default'   => ['Templates/Page/Default.html'],
            'partial Header'     => ['Partials/Page/Header.html'],
            'partial Footer'     => ['Partials/Page/Footer.html'],
            'partial Navigation' => ['Partials/Page/Navigation.html'],
        ];
    }

    #[DataProvider('allTemplateFilesProvider')]
    public function testTemplateFileIsNotEmpty(string $relativePath): void
    {
        $html = $this->readTemplate($relativePath);
        $this->assertNotEmpty(trim($html), "Template '{$relativePath}' must not be empty.");
    }

    #[DataProvider('allTemplateFilesProvider')]
    public function testTemplateFileHasNoPhpOpenTag(string $relativePath): void
    {
        $html = $this->readTemplate($relativePath);
        $this->assertStringNotContainsString('<?php', $html, "Template '{$relativePath}' must not contain raw PHP.");
    }
}
