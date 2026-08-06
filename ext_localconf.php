<?php

declare(strict_types=1);

use Maispace\MaiTheme\Components\ComponentCollection;
use Maispace\MaiTheme\Service\BackendThemeService;

defined('TYPO3') or exit('Access denied.');

BackendThemeService::applyTheme();

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['form']['persistenceManager']['allowedExtensionPaths'][] =
    'EXT:mai_theme/Resources/Private/Forms/';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['form']['persistenceManager']['allowSaveToExtensionPaths'] = true;

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['form']['yamlConfigurations'][1700000001] =
    'EXT:mai_theme/Configuration/Yaml/FormSetup.yaml';

$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['mai_theme_default'] =
    'EXT:mai_theme/Configuration/RTE/Default.yaml';

// Global Fluid namespace: <theme:atom.button>, <theme:molecule.card>, etc.
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['theme'] = [ComponentCollection::class];
