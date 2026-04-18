<?php

declare(strict_types=1);

defined('TYPO3') or die();

use Maispace\MaiBase\TableConfigurationArray\CType;

// Generic text+media CType (parent for image/media variants rendered
// through the `theme:molecule.mediaText` Fluid Component).
new CType('maispace_textmedia', 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:ctype.textmedia', 'content-textpic')
    ->addDefaultHeaderPalette()
    ->addCustomFields('bodytext', 'image', 'imageorient', 'imagewidth')
    ->register();
