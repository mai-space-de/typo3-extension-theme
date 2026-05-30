<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$typo3Version = GeneralUtility::makeInstance(Typo3Version::class);

if ($typo3Version->getMajorVersion() >= 13) {
    // Page-TSconfig layouts live under the "pagets" data provider (pagets__default).
    // The core "default" provider only exposes colPos 0 and breaks Visual Editor.
    $GLOBALS['TCA']['pages']['columns']['backend_layout']['config']['default'] = 'pagets__default';
}
