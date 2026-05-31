<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Templates;

use PHPUnit\Framework\TestCase;

/**
 * Smoke tests for page-level H1 rendering in mai_theme page templates.
 */
final class PageHeadingTest extends TestCase
{
    private const PARTIAL_PATH = __DIR__ . '/../../../Resources/Private/Partials/Molecule/PageHeading.html';

    private const DEFAULT_TEMPLATE_PATH = __DIR__ . '/../../../Resources/Private/Templates/Page/Default.html';

    private const LANDING_TEMPLATE_PATH = __DIR__ . '/../../../Resources/Private/Templates/Page/Landing.html';

    public function testPageHeadingPartialExistsAndRendersH1(): void
    {
        self::assertFileExists(self::PARTIAL_PATH);

        $contents = (string) file_get_contents(self::PARTIAL_PATH);

        self::assertStringContainsString('data-namespace-typo3-fluid="true"', $contents);
        self::assertStringContainsString('<h1 class="mai-page__title">', $contents);
        self::assertStringContainsString('{pageHeading}', $contents);
    }

    public function testDefaultPageTemplateIncludesPageHeadingInMain(): void
    {
        $contents = (string) file_get_contents(self::DEFAULT_TEMPLATE_PATH);

        self::assertMatchesRegularExpression(
            '/<f:if condition="\{hero\}">[\s\S]*partial="Molecule\/PageHeading"[\s\S]*arguments="\{pageHeading: pageHeading\}"/',
            $contents,
            'Default.html must render PageHeading only when no Hero CE is present',
        );
    }

    public function testLandingPageTemplateIncludesPageHeadingInMain(): void
    {
        $contents = (string) file_get_contents(self::LANDING_TEMPLATE_PATH);

        self::assertMatchesRegularExpression(
            '/partial="Molecule\/PageHeading"[\s\S]*arguments="\{pageHeading: pageHeading\}"/',
            $contents,
            'Landing.html must pass pageHeading into the PageHeading partial',
        );
    }

    public function testPageTyposcriptDefinesPageHeadingVariable(): void
    {
        $contents = (string) file_get_contents(
            __DIR__ . '/../../../Configuration/TypoScript/Setup/page.typoscript',
        );

        self::assertStringContainsString('pageHeading = TEXT', $contents);
        self::assertStringContainsString('field = nav_title // title', $contents);
        self::assertStringContainsString('HeroContentProcessor', $contents);
    }
}
