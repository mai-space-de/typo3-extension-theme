<?php

declare(strict_types=1);

use Maispace\MaiTheme\Service\BackendThemeService;

defined('TYPO3') or exit('Access denied.');

// Apply backend theme (logo, favicon, login colors, ...) early.
BackendThemeService::applyTheme();

// ---- content-defender: merge child-CType restrictions ------------------
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['content_defender']['tt_content'] = array_merge(
    (array)($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['content_defender']['tt_content'] ?? []),
    (array)(@include __DIR__ . '/Configuration/ContentElements/section.php'),
    (array)(@include __DIR__ . '/Configuration/ContentElements/bento.php'),
);

// ---- TYPO3 Form: register custom setup ---------------------------------
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['form']['persistenceManager']['allowedExtensionPaths'][] =
    'EXT:mai_theme/Resources/Private/Forms/';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['form']['persistenceManager']['allowSaveToExtensionPaths'] = true;

// Register the theme's Form YAML override
\TYPO3\CMS\Core\Configuration\ConfigurationManager::class; // force autoload
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][] =
    'EXT:mai_theme/Configuration/Yaml/FormSetup.yaml';

// ---- RTE CKEditor preset -----------------------------------------------
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['mai_theme_default'] =
    'EXT:mai_theme/Configuration/RTE/Default.yaml';
