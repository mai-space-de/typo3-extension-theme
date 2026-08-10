<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Templates;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Guards against double-escaping of f:render.text output in atom.heading.
 */
final class AtomHeadingEscapingTest extends TestCase
{
    private const HEADING_PATH = __DIR__ . '/../../../Resources/Private/Components/Atom/Heading.fluid.html';

    #[Test]
    public function headingOutputsTextViaFormatRaw(): void
    {
        self::assertFileExists(self::HEADING_PATH);
        $contents = (string) file_get_contents(self::HEADING_PATH);

        self::assertStringContainsString('{text -> f:format.raw()}', $contents);
        self::assertDoesNotMatchRegularExpression(
            '/<(?:h[1-6])[^>]*>\{text\}<\/(?:h[1-6])>/',
            $contents,
            'atom.heading must not auto-escape {text}; callers pass f:render.text (already escaped).',
        );
    }
}
