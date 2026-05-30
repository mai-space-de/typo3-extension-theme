<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

$typo3Version = GeneralUtility::makeInstance(Typo3Version::class);

if ($typo3Version->getMajorVersion() >= 13) {
    $GLOBALS['TCA']['pages']['columns']['backend_layout']['config']['default'] = 'default';
}
