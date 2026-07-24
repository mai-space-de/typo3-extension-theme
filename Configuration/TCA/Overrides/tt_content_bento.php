<?php

declare(strict_types=1);

defined('TYPO3') or die();

if (class_exists(\B13\Container\Tca\Registry::class)) {
    $registry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class);
    $lll = 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:';

    $registry->configureContainer(
        new \B13\Container\Tca\ContainerConfiguration(
            'maispace_bento',
            $lll . 'ctype.bento',
            $lll . 'ctype.bento.description',
            [
                [
                    [
                        'name' => $lll . 'col.bento',
                        'colPos' => 200,
                    ],
                ],
            ],
        )->setIcon('content-dashboard')
            ->setGroup('maispace_sections'),
    );
}

$GLOBALS['TCA']['tt_content']['columns']['tx_maitheme_bento_colspan'] = [
    'label' => 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:bento.colspan',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            ['label' => '3 / 12', 'value' => '3'],
            ['label' => '4 / 12', 'value' => '4'],
            ['label' => '6 / 12', 'value' => '6'],
            ['label' => '8 / 12', 'value' => '8'],
            ['label' => '12 / 12', 'value' => '12'],
        ],
        'default' => '4',
    ],
];

$GLOBALS['TCA']['tt_content']['columns']['tx_maitheme_bento_rowspan'] = [
    'label' => 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:bento.rowspan',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            ['label' => '1', 'value' => '1'],
            ['label' => '2', 'value' => '2'],
            ['label' => '3', 'value' => '3'],
        ],
        'default' => '1',
    ],
];

$GLOBALS['TCA']['tt_content']['columns']['tx_maitheme_bento_variant'] = [
    'label' => 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:bento.variant',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            ['label' => 'Card', 'value' => 'card'],
            ['label' => 'Highlight', 'value' => 'highlight'],
            ['label' => 'Media', 'value' => 'media'],
        ],
        'default' => 'card',
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', []);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--palette--;;bentoOptions',
    'maispace_text,maispace_textmedia',
    'after:header'
);

$GLOBALS['TCA']['tt_content']['palettes']['bentoOptions'] = [
    'showitem' => 'tx_maitheme_bento_colspan, tx_maitheme_bento_rowspan, tx_maitheme_bento_variant',
];
