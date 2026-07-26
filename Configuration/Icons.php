<?php

declare(strict_types=1);

use Maispace\MaiTheme\Icon\ContentIconCatalog;

return array_merge(
    [
        'ext-maispace-mai_theme' => [
            'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            'source' => 'EXT:mai_theme/Resources/Public/Icons/Extension.svg',
        ],
    ],
    ContentIconCatalog::iconRegistryEntries(),
);
