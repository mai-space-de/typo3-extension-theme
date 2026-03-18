<?php

declare(strict_types = 1);

/**
 * PSR-15 middleware registration for maispace/theme.
 *
 * Registers the BackendThemeFromSiteSettings middleware in the TYPO3 backend
 * request pipeline. It runs early in the stack so that backend theme values
 * (logos, login-page colors etc.) are applied before the backend renders.
 */
return [
    'backend' => [
        'maispace/theme/backend-theme-from-site-settings' => [
            'target' => \Maispace\Theme\Middleware\BackendThemeFromSiteSettings::class,
            'before' => [
                'typo3/cms-backend/output-compression',
            ],
        ],
    ],
];
