<?php

defined('TYPO3') or exit('Access denied.');

// Customize Backend Login
(new \Maispace\MaiTheme\Services\BackendTheme)->registerBackendTheme();

// Register Fluid Components namespace so templates can use {namespace theme=Maispace\MaiTheme\Components}
// and render components as <theme:atom.button>, <theme:molecule.card>, <theme:organism.siteHeader> etc.
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fluid_components']['namespaces']['Maispace\\MaiTheme\\Components'] =
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mai_theme', 'Resources/Private/Components');
