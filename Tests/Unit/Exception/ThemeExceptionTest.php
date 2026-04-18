<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Exception;

use Maispace\MaiTheme\Exception\ComponentRenderException;
use Maispace\MaiTheme\Exception\InvalidThemeConfigurationException;
use Maispace\MaiTheme\Exception\ThemeException;
use PHPUnit\Framework\TestCase;

final class ThemeExceptionTest extends TestCase
{
    public function testAllThemeExceptionsExtendThemeException(): void
    {
        self::assertTrue(is_subclass_of(InvalidThemeConfigurationException::class, ThemeException::class));
        self::assertTrue(is_subclass_of(ComponentRenderException::class, ThemeException::class));
    }

    public function testThemeExceptionExtendsRuntimeException(): void
    {
        $e = new ThemeException('boom', 1_736_000_999);
        self::assertInstanceOf(\RuntimeException::class, $e);
        self::assertSame('boom', $e->getMessage());
        self::assertSame(1_736_000_999, $e->getCode());
    }
}
