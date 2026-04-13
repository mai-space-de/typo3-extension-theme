<?php

declare(strict_types=1);

defined('TYPO3') or die();

$lll = 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:';

return [
    'ctrl' => [
        'title' => $lll . 'item.timeline',
        'label' => 'title',
        'sortby' => 'sort',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'hideTable' => true,
        'typeicon_classes' => ['default' => 'content-list-bullet'],
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => ['type' => 'check', 'renderType' => 'checkboxToggle'],
        ],
        'sys_language_uid' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => ['type' => 'language'],
        ],
        'l10n_parent' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [['label' => '', 'value' => 0]],
                'foreign_table' => 'tx_maitheme_timeline_item',
                'foreign_table_where' => 'AND {#tx_maitheme_timeline_item}.{#pid}=###CURRENT_PID### AND {#tx_maitheme_timeline_item}.{#sys_language_uid} IN (-1,0)',
                'default' => 0,
            ],
        ],
        'parent_uid' => [
            'config' => ['type' => 'passthrough'],
        ],
        'sort' => [
            'config' => ['type' => 'passthrough'],
        ],
        'event_date' => [
            'label' => $lll . 'item.event_date',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
            ],
        ],
        'title' => [
            'label' => $lll . 'item.title',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'description' => [
            'label' => $lll . 'item.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'rows' => 6,
                'cols' => 60,
            ],
        ],
        'image' => [
            'label' => $lll . 'item.image',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
                'maxitems' => 1,
                'appearance' => [
                    'createNewRelationLinkTitle' => 'Add image',
                ],
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' =>
                'hidden, sys_language_uid, l10n_parent,' .
                '--div--;' . $lll . 'tab.content,' .
                'event_date, title, description, image',
        ],
    ],
];
