<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Event;

/**
 * Dispatched after BackendThemeService has applied its settings to
 * $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']. Listeners may inspect
 * or further adjust the applied configuration.
 */
final class AfterBackendThemeAppliedEvent
{
    public function __construct(
        private array $appliedSettings,
    ) {}

    public function getAppliedSettings(): array
    {
        return $this->appliedSettings;
    }

    public function setAppliedSettings(array $appliedSettings): void
    {
        $this->appliedSettings = $appliedSettings;
    }
}
