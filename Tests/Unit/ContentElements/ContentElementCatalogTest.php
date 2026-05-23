<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\ContentElements;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Catalog integrity tests for content-element Fluid templates.
 *
 * These tests verify that every `templateName` value referenced in
 * `contentElement.typoscript` has a corresponding `.html` file in
 * `Resources/Private/Templates/ContentElements/`, that each file is
 * non-empty, and that each file carries the required Fluid HTML-namespace
 * attribute (`data-namespace-typo3-fluid="true"`).
 *
 * These are **smoke tests** — they do not test rendering. Their purpose is
 * to catch configuration drift early: if a template is renamed, deleted, or
 * a new CType is registered without an accompanying template, these tests
 * fail immediately.
 *
 * @see EXT:mai_theme/Configuration/TypoScript/Setup/lib/contentElement.typoscript
 */
final class ContentElementCatalogTest extends TestCase
{
    /**
     * Absolute path to the ContentElements template directory.
     * Resolved relative to this test file: Tests/Unit/ContentElements/ → ../../..
     */
    private const TEMPLATE_DIR = __DIR__ . '/../../../Resources/Private/Templates/ContentElements';

    // -------------------------------------------------------------------------
    // Data provider
    // -------------------------------------------------------------------------

    /**
     * All template names referenced by `contentElement.typoscript`.
     *
     * Each entry corresponds to one `templateName = <Name>` line in the
     * TypoScript configuration and maps 1:1 to `<Name>.html` on disk.
     *
     * CTypes that share a template (e.g. `maispace_faq` and `maispace_steps`
     * both inherit from `tt_content.maispace_accordion`) appear only once here
     * because they resolve to the same file. Similarly, `maispace_tab`
     * inherits from `maispace_tabs` (templateName = Tab).
     *
     * @return array<string, array{string}>
     */
    public static function templateNamesProvider(): array
    {
        return [
            // Basic group ─────────────────────────────────────────────────────
            'Alert'        => ['Alert'],
            'Audio'        => ['Audio'],
            'Breadcrumb'   => ['Breadcrumb'],
            'Button'       => ['Button'],
            'Callout'      => ['Callout'],
            'Divider'      => ['Divider'],
            'Embed'        => ['Embed'],
            'Heading'      => ['Heading'],
            'Icon'         => ['Icon'],
            'Iconlist'     => ['Iconlist'],
            'Linklist'     => ['Linklist'],
            'Richtext'     => ['Richtext'],
            'Socialmedia'  => ['Socialmedia'],
            'Spacer'       => ['Spacer'],
            'Text'         => ['Text'],
            'Textcolumns'  => ['Textcolumns'],

            // Page group ───────────────────────────────────────────────────────
            'Banner'       => ['Banner'],
            'Cta'          => ['Cta'],
            'Hero'         => ['Hero'],
            'Mediatext'    => ['Mediatext'],

            // Components group ─────────────────────────────────────────────────
            'Badge'        => ['Badge'],
            'Card'         => ['Card'],
            'Contactinfo'  => ['Contactinfo'],
            'Featurebox'   => ['Featurebox'],
            'Logo'         => ['Logo'],
            'Logoshowcase' => ['Logoshowcase'],
            'Profile'      => ['Profile'],
            'Quote'        => ['Quote'],
            'Statistic'    => ['Statistic'],
            'Teaser'       => ['Teaser'],
            'Testimonial'  => ['Testimonial'],

            // Widgets group ────────────────────────────────────────────────────
            // maispace_faq and maispace_steps inherit from maispace_accordion
            // and share the Accordion template.
            // maispace_tab inherits from maispace_tabs (templateName = Tab).
            // maispace_timeline is registered and rendered by EXT:mai_timeline.
            'Accordion'    => ['Accordion'],
            'Beforeafter'  => ['Beforeafter'],
            'Image'        => ['Image'],
            'Modal'        => ['Modal'],
            'Slider'       => ['Slider'],
            'Tab'          => ['Tab'],
            'TextMedia'    => ['TextMedia'],
            'Video'        => ['Video'],

            // Forms group ──────────────────────────────────────────────────────
            'Form'         => ['Form'],
            'Map'          => ['Map'],
            'Newsletter'   => ['Newsletter'],

            // Data group ───────────────────────────────────────────────────────
            'Chart'        => ['Chart'],
            'Codeblock'    => ['Codeblock'],
            'Datalist'     => ['Datalist'],
            'Filelist'     => ['Filelist'],
            'Gallery'      => ['Gallery'],
            'Progressbar'  => ['Progressbar'],
            'Rating'       => ['Rating'],
            'Table'        => ['Table'],

            // Section containers (b13/container) ──────────────────────────────
            'Section3366'     => ['Section3366'],
            'Section3Col'     => ['Section3Col'],
            'Section4Col'     => ['Section4Col'],
            'Section5050'     => ['Section5050'],
            'Section6633'     => ['Section6633'],
            'SectionFull'     => ['SectionFull'],
            'SectionSidebarL' => ['SectionSidebarL'],
            'SectionSidebarR' => ['SectionSidebarR'],

            // Bento grid (b13/container) ───────────────────────────────────────
            'BentoBox'     => ['BentoBox'],
        ];
    }

    // -------------------------------------------------------------------------
    // Tests
    // -------------------------------------------------------------------------

    /**
     * Every templateName in contentElement.typoscript must have a
     * corresponding .html file on disk.
     *
     * A missing file means the CType is registered and routing is configured
     * but TYPO3 Fluid cannot render it — the page request will throw a
     * Fluid TemplateNotFoundException in production.
     */
    #[DataProvider('templateNamesProvider')]
    public function testTemplateFileExists(string $templateName): void
    {
        $filePath = sprintf('%s/%s.html', self::TEMPLATE_DIR, $templateName);

        self::assertFileExists(
            $filePath,
            sprintf(
                'Content-element template "%s.html" is missing from %s. '
                    . 'Every templateName referenced in contentElement.typoscript '
                    . 'must have a corresponding .html file.',
                $templateName,
                self::TEMPLATE_DIR,
            ),
        );
    }

    /**
     * Template files must not be empty — a zero-byte file would silently
     * render nothing in the frontend without triggering an error.
     */
    #[DataProvider('templateNamesProvider')]
    public function testTemplateFileIsNotEmpty(string $templateName): void
    {
        $filePath = sprintf('%s/%s.html', self::TEMPLATE_DIR, $templateName);

        if (!file_exists($filePath)) {
            self::markTestSkipped(sprintf(
                'Template %s.html does not exist — covered by testTemplateFileExists.',
                $templateName,
            ));
        }

        $content = (string) file_get_contents($filePath);
        self::assertNotEmpty(
            trim($content),
            sprintf(
                'Template "%s.html" is empty. Even a stub template must contain '
                    . 'at least the Fluid HTML wrapper.',
                $templateName,
            ),
        );
    }

    /**
     * All templates must use the HTML-namespace form with
     * `data-namespace-typo3-fluid="true"` so that Fluid resolves ViewHelper
     * calls correctly and strips the outer `<html>` tag from the rendered
     * output.
     *
     * The alternative `{namespace f=...}` inline syntax is intentionally
     * discouraged: it renders visibly in the HTML source when the template
     * is served without Fluid processing (e.g. during static-file debugging).
     */
    #[DataProvider('templateNamesProvider')]
    public function testTemplateHasFluidNamespaceDeclaration(string $templateName): void
    {
        $filePath = sprintf('%s/%s.html', self::TEMPLATE_DIR, $templateName);

        if (!file_exists($filePath)) {
            self::markTestSkipped(sprintf(
                'Template %s.html does not exist — covered by testTemplateFileExists.',
                $templateName,
            ));
        }

        $content = (string) file_get_contents($filePath);
        self::assertStringContainsString(
            'data-namespace-typo3-fluid="true"',
            $content,
            sprintf(
                'Template "%s.html" is missing the required Fluid HTML-namespace '
                    . 'attribute `data-namespace-typo3-fluid="true"`. '
                    . 'All content-element templates must open with '
                    . '`<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers" '
                    . 'data-namespace-typo3-fluid="true">` '
                    . 'for correct TYPO3 Fluid rendering.',
                $templateName,
            ),
        );
    }
}
