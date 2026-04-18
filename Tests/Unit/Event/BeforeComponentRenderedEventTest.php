<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\Event;

use Maispace\MaiTheme\Event\BeforeComponentRenderedEvent;
use PHPUnit\Framework\TestCase;

final class BeforeComponentRenderedEventTest extends TestCase
{
    public function testExposesIdentifierAndArguments(): void
    {
        $event = new BeforeComponentRenderedEvent('atom.button', ['label' => 'Click']);
        self::assertSame('atom.button', $event->getComponentIdentifier());
        self::assertSame(['label' => 'Click'], $event->getArguments());
        self::assertFalse($event->isCancelled());
    }

    public function testAllowsArgumentMutation(): void
    {
        $event = new BeforeComponentRenderedEvent('atom.button', ['label' => 'Click']);
        $event->setArguments(['label' => 'Weiter']);
        self::assertSame(['label' => 'Weiter'], $event->getArguments());
    }

    public function testCancelFlag(): void
    {
        $event = new BeforeComponentRenderedEvent('atom.button', []);
        $event->cancel();
        self::assertTrue($event->isCancelled());
    }
}
