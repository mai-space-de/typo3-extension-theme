<?php

defined('TYPO3') or exit('Access denied.');

// Customize Backend Login
(new \Maispace\Theme\Services\BackendTheme)->registerBackendTheme();

// Register Fluid Components namespace so templates can use {namespace theme=Maispace\Theme\Components}
// and render components as <theme:atom.button>, <theme:molecule.card>, <theme:organism.siteHeader> etc.
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fluid_components']['namespaces']['Maispace\\Theme\\Components'] =
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('theme', 'Resources/Private/Components');
