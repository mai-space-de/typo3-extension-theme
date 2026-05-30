<?php

declare(strict_types=1);

/**
 * Font configuration for mai_theme.
 *
 * Fonts declared here are automatically:
 * - Preloaded via FontPreloadCollector
 * - Registered as HTTP 103 Early Hint candidates
 * - Self-hosted from EXT:mai_theme/Resources/Public/Fonts/
 *
 * @see \Maispace\MaiAssets\Configuration\ExtensionConfigurationDiscovery::discoverFonts()
 */
return [
    'sites' => ['*'],
    'fonts' => [
        [
            'src'  => 'EXT:mai_theme/Resources/Public/Fonts/SourceSans3-wght.ttf',
            'type' => 'truetype',
        ],
        [
            'src'  => 'EXT:mai_theme/Resources/Public/Fonts/SourceSerif4-wght.ttf',
            'type' => 'truetype',
        ],
    ],
];
