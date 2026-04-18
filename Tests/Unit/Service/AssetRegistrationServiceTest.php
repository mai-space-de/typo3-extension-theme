<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Service;

use Maispace\MaiTheme\Event\BeforeComponentRenderedEvent;
use Maispace\MaiTheme\Exception\AssetRegistrationException;
use Maispace\MaiTheme\Exception\ComponentRenderException;
use Maispace\MaiTheme\Exception\InvalidThemeConfigurationException;
use Maispace\MaiTheme\Exception\ThemeException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the asset-registration path.
 *
 * Note: `AssetRegistrationService` depends on `mai_assets` processors, the
 * TYPO3 `AssetCollector`, and `mai_assets`' `ExtensionConfiguration`. Fully
 * instantiating the service in a unit test requires a TYPO3 bootstrap which
 * is out of scope for this suite. We therefore focus on:
 *  - exception hierarchy shape (all extension exceptions inherit from
 *    `ThemeException` / `\RuntimeException`),
 *  - `BeforeComponentRenderedEvent` mutation + cancellation semantics.
 */
final class AssetRegistrationServiceTest extends TestCase
{
    public function testAllThemeExceptionsExtendThemeException(): void
    {
        self::assertTrue(is_subclass_of(InvalidThemeConfigurationException::class, ThemeException::class));
        self::assertTrue(is_subclass_of(AssetRegistrationException::class, ThemeException::class));
        self::assertTrue(is_subclass_of(ComponentRenderException::class, ThemeException::class));
    }

    public function testThemeExceptionExtendsRuntimeException(): void
    {
        $e = new ThemeException('boom', 1_736_000_999);
        self::assertInstanceOf(\RuntimeException::class, $e);
        self::assertSame('boom', $e->getMessage());
        self::assertSame(1_736_000_999, $e->getCode());
    }

    public function testAssetRegistrationExceptionMessageCarriesPath(): void
    {
        $e = new AssetRegistrationException('Unable to read theme stylesheet "/tmp/x.scss".', 1_736_000_010);
        self::assertStringContainsString('/tmp/x.scss', $e->getMessage());
    }

    public function testBeforeComponentRenderedEventExposesIdentifierAndArguments(): void
    {
        $event = new BeforeComponentRenderedEvent('atom.button', ['label' => 'Click']);
        self::assertSame('atom.button', $event->getComponentIdentifier());
        self::assertSame(['label' => 'Click'], $event->getArguments());
        self::assertFalse($event->isCancelled());
    }

    public function testBeforeComponentRenderedEventAllowsArgumentMutation(): void
    {
        $event = new BeforeComponentRenderedEvent('atom.button', ['label' => 'Click']);
        $event->setArguments(['label' => 'Weiter']);
        self::assertSame(['label' => 'Weiter'], $event->getArguments());
    }

    public function testBeforeComponentRenderedEventCancelFlag(): void
    {
        $event = new BeforeComponentRenderedEvent('atom.button', []);
        $event->cancel();
        self::assertTrue($event->isCancelled());
    }
}
