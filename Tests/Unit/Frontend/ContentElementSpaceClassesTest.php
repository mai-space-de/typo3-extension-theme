<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Frontend;

use Maispace\MaiTheme\Frontend\ContentElementSpaceClasses;
use PHPUnit\Framework\TestCase;

final class ContentElementSpaceClassesTest extends TestCase
{
    public function testEmptyValueAddsNothing(): void
    {
        $html = '<article class="mai-text" id="c1">Text</article>';

        self::assertSame($html, ContentElementSpaceClasses::inject($html, '', ''));
    }

    public function testUnknownLegacyValueAddsNothing(): void
    {
        $html = '<article class="mai-text">Text</article>';

        self::assertSame($html, ContentElementSpaceClasses::inject($html, 'extra-small', 'large'));
    }

    public function testTokensAreAddedToTheExistingClassAttribute(): void
    {
        $result = ContentElementSpaceClasses::inject(
            '<article class="mai-text" id="c12">Text</article>',
            'md',
            '2xl',
        );

        self::assertSame(
            '<article class="mai-text mai-ce-space-before-md mai-ce-space-after-2xl" id="c12">Text</article>',
            $result,
        );
    }

    public function testClassAttributeIsCreatedWhenMissing(): void
    {
        $result = ContentElementSpaceClasses::inject('<section id="c3">Text</section>', '4xl', '');

        self::assertSame(
            '<section id="c3" class="mai-ce-space-before-4xl">Text</section>',
            $result,
        );
    }

    public function testLeadingCommentIsSkipped(): void
    {
        $result = ContentElementSpaceClasses::inject(
            "<!-- note -->\n<div class=\"mai-hero\">Text</div>",
            '',
            'xs',
        );

        self::assertSame(
            "<!-- note -->\n<div class=\"mai-hero mai-ce-space-after-xs\">Text</div>",
            $result,
        );
    }
}
