<?php

use Maispace\MaiTheme\Service\BackendThemeService;

defined('TYPO3') or exit('Access denied.');

BackendThemeService::applyTheme();

// ============================================================================
// SECTION CONTAINERS (b13/container)
// colPos range 200–299 — no conflict with core layout colPos 0–6
// ============================================================================

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('container')) {
    $lll = 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:';

    \B13\Container\Tca\Registry::configureContainer(
        (new \B13\Container\Tca\ContainerConfiguration(
            'maispace_section_full',
            $lll . 'ctype.section_full',
            '',
            [[
                ['name' => $lll . 'col.content', 'colPos' => 250, 'colspan' => 12],
            ]]
        ))->setIcon('content-container-columns-1')
    );

    \B13\Container\Tca\Registry::configureContainer(
        (new \B13\Container\Tca\ContainerConfiguration(
            'maispace_section_50_50',
            $lll . 'ctype.section_50_50',
            '',
            [[
                ['name' => $lll . 'col.left',  'colPos' => 200, 'colspan' => 6],
                ['name' => $lll . 'col.right', 'colPos' => 201, 'colspan' => 6],
            ]]
        ))->setIcon('content-container-columns-2')
    );

    \B13\Container\Tca\Registry::configureContainer(
        (new \B13\Container\Tca\ContainerConfiguration(
            'maispace_section_66_33',
            $lll . 'ctype.section_66_33',
            '',
            [[
                ['name' => $lll . 'col.wide',   'colPos' => 210, 'colspan' => 8],
                ['name' => $lll . 'col.narrow', 'colPos' => 211, 'colspan' => 4],
            ]]
        ))->setIcon('content-container-columns-2')
    );

    \B13\Container\Tca\Registry::configureContainer(
        (new \B13\Container\Tca\ContainerConfiguration(
            'maispace_section_33_66',
            $lll . 'ctype.section_33_66',
            '',
            [[
                ['name' => $lll . 'col.narrow', 'colPos' => 220, 'colspan' => 4],
                ['name' => $lll . 'col.wide',   'colPos' => 221, 'colspan' => 8],
            ]]
        ))->setIcon('content-container-columns-2')
    );

    \B13\Container\Tca\Registry::configureContainer(
        (new \B13\Container\Tca\ContainerConfiguration(
            'maispace_section_3col',
            $lll . 'ctype.section_3col',
            '',
            [[
                ['name' => $lll . 'col.col1', 'colPos' => 230, 'colspan' => 4],
                ['name' => $lll . 'col.col2', 'colPos' => 231, 'colspan' => 4],
                ['name' => $lll . 'col.col3', 'colPos' => 232, 'colspan' => 4],
            ]]
        ))->setIcon('content-container-columns-3')
    );

    \B13\Container\Tca\Registry::configureContainer(
        (new \B13\Container\Tca\ContainerConfiguration(
            'maispace_section_4col',
            $lll . 'ctype.section_4col',
            '',
            [[
                ['name' => $lll . 'col.col1', 'colPos' => 240, 'colspan' => 3],
                ['name' => $lll . 'col.col2', 'colPos' => 241, 'colspan' => 3],
                ['name' => $lll . 'col.col3', 'colPos' => 242, 'colspan' => 3],
                ['name' => $lll . 'col.col4', 'colPos' => 243, 'colspan' => 3],
            ]]
        ))->setIcon('content-container-columns-4')
    );

    \B13\Container\Tca\Registry::configureContainer(
        (new \B13\Container\Tca\ContainerConfiguration(
            'maispace_section_sidebar_r',
            $lll . 'ctype.section_sidebar_r',
            '',
            [[
                ['name' => $lll . 'col.main',    'colPos' => 260, 'colspan' => 9],
                ['name' => $lll . 'col.sidebar', 'colPos' => 261, 'colspan' => 3],
            ]]
        ))->setIcon('content-container-columns-2')
    );

    \B13\Container\Tca\Registry::configureContainer(
        (new \B13\Container\Tca\ContainerConfiguration(
            'maispace_section_sidebar_l',
            $lll . 'ctype.section_sidebar_l',
            '',
            [[
                ['name' => $lll . 'col.sidebar', 'colPos' => 270, 'colspan' => 3],
                ['name' => $lll . 'col.main',    'colPos' => 271, 'colspan' => 9],
            ]]
        ))->setIcon('content-container-columns-2')
    );
}