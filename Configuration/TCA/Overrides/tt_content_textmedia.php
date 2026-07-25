<?php

declare(strict_types=1);

defined('TYPO3') or die();

use Maispace\MaiBase\TableConfigurationArray\CType;

// Generic text+media CType (parent for image/media variants rendered
// through the `theme:molecule.mediaText` Fluid Component).
new CType('maispace_textmedia', 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:ctype.textmedia', 'content-textpic')
    ->addDefaultHeaderPalette()
    ->addCustomFields('bodytext, image, imageorient, imagewidth')
    // TYPO3 14: bodytext has no RTE by default — must enable via columnsOverrides.
    ->addColumnOverride('bodytext', ['config' => ['enableRichtext' => true]])
    // This component only renders a genuine two-column layout (Molecule/MediaText),
    // so only the two core "beside text" imageorient values apply here.
    ->addColumnOverride('imageorient', [
        'config' => [
            'items' => [
                [
                    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.9',
                    'value' => 25,
                ],
                [
                    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.10',
                    'value' => 26,
                ],
            ],
            'default' => 25,
        ],
    ])
    ->register();
