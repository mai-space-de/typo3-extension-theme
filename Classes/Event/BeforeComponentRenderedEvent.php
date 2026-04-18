<?php
declare(strict_types=1);
namespace Maispace\MaiTheme\Event;

/**
 * Dispatched before a Fluid Component template is rendered.
 * Listeners may modify the component arguments or cancel rendering entirely.
 */
final class BeforeComponentRenderedEvent
{
    private bool $cancelled = false;

    public function __construct(
        private readonly string $componentIdentifier,
        private array $arguments,
    ) {}

    public function getComponentIdentifier(): string
    {
        return $this->componentIdentifier;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function cancel(): void
    {
        $this->cancelled = true;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }
}
