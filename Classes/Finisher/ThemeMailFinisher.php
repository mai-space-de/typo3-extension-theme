<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Finisher;

use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;

/**
 * Stub finisher — delegates to `mai_mail` for actual dispatch per AGENTS.md
 * architecture constraint ("mai_mail exclusively dispatches email"). This
 * finisher only collects the form data and hands it off to `mai_mail` via
 * its public API; it MUST NOT pull in `symfony/mailer` or send mail itself.
 */
final class ThemeMailFinisher extends AbstractFinisher
{
    protected $defaultOptions = [
        'subject' => '',
        'recipient' => '',
        'template' => '',
    ];

    protected function executeInternal(): ?string
    {
        // Intentionally left as a stub: real dispatch lives in `mai_mail`.
        // Implementers should inject `mai_mail`'s queue service and enqueue
        // a message built from `$this->finisherContext->getFormValues()`.
        return null;
    }
}
