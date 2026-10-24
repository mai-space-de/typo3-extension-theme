<?php

declare(strict_types=1);

defined('TYPO3') or die();

// ============================================================================
// PLUGIN CONTENT ELEMENT TAB STRUCTURE
//
// Applies the shared maispace design/advanced/language/access tabs to every
// plugin-type CType registered by feature extensions. This file runs after all
// feature extensions' TCA/Overrides/tt_content.php files have registered their
// CTypes (alphabetically mai_theme > all mai_* feature extensions).
//
// Only showitem is overridden — columnsOverrides (FlexForm links, etc.) set by
// each extension's registerPlugin() call are preserved.
// ============================================================================

$lll = static fn(string $path): string => 'LLL:EXT:' . $path;

$tabGeneral = $lll('core/Resources/Private/Language/Form/locallang_tabs.xlf:general');
$tabAppearance = $lll('mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:tab.appearance_layout');
$tabAdvanced = $lll('mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:tab.advanced');
$tabLanguage = $lll('core/Resources/Private/Language/Form/locallang_tabs.xlf:language');
$tabAccess = $lll('core/Resources/Private/Language/Form/locallang_tabs.xlf:access');

$sharedTabs =
    '--div--;' . $tabAppearance . ',' .
    '--palette--;;maispace_design,--palette--;;maispace_spacing,--palette--;;maispace_responsive,' .
    '--div--;' . $tabAdvanced . ',' .
    '--palette--;;maispace_animation,--palette--;;maispace_linking,--palette--;;maispace_advanced,' .
    '--div--;' . $tabLanguage . ',' .
    '--palette--;;language,' .
    '--div--;' . $tabAccess . ',' .
    '--palette--;;hidden,--palette--;;access';

// Tab 1 content for plugin CEs with a FlexForm (pi_flexform carries filter/view config).
$tab1WithFlexForm =
    '--div--;' . $tabGeneral . ',' .
    '--palette--;;general,header,pi_flexform,';

// Tab 1 content for plugin CEs without a FlexForm (no configuration needed on tab 1).
$tab1WithoutFlexForm =
    '--div--;' . $tabGeneral . ',' .
    '--palette--;;general,header,';

// ─── CTypes with FlexForm ────────────────────────────────────────────────────
$withFlexForm = [
    // EXT:mai_account
    'maiaccount_account',
    'maiaccount_register',
    'maiaccount_stories',
    // EXT:mai_canteen
    'maicanteen_week',
    'maicanteen_print',
    // EXT:mai_events (mai_events_view is registered manually; maievents_list via registerPlugin)
    'mai_events_view',
    'maievents_list',
    // EXT:mai_faq
    'maifaq_list',
    // EXT:mai_jobs
    'maijobs_list',
    // EXT:mai_locations
    'mailocations_list',
    // EXT:mai_member
    'maimember_view',
    'maimember_list',
    // EXT:mai_news
    'mainews_list',
    'mainews_rss',
    // EXT:mai_resource
    'mairesource_list',
    // EXT:mai_survey
    'maisurvey_survey',
    // EXT:mai_team
    'maiteam_view',
    // EXT:mai_testimonials
    'maitestimonials_list',
    'maitestimonials_slider',
    'maitestimonials_single',
];

// ─── CTypes without FlexForm ─────────────────────────────────────────────────
$withoutFlexForm = [
    // EXT:mai_account
    'maiaccount_mfa',
    // EXT:mai_events
    'maievents_registration',
    // EXT:mai_jobs
    'maijobs_detail',
    // EXT:mai_locations
    'mailocations_detail',
    // EXT:mai_member
    'maimember_application',
    // EXT:mai_newsletter (newsletter signup form — no list config)
    'mainewsletter_newsletter',
    // EXT:mai_search
    'maisearch_search',
];

foreach ($withFlexForm as $cType) {
    if (!isset($GLOBALS['TCA']['tt_content']['types'][$cType])) {
        $GLOBALS['TCA']['tt_content']['types'][$cType] = [];
    }
    $GLOBALS['TCA']['tt_content']['types'][$cType]['showitem'] = $tab1WithFlexForm . $sharedTabs;
}

foreach ($withoutFlexForm as $cType) {
    if (!isset($GLOBALS['TCA']['tt_content']['types'][$cType])) {
        $GLOBALS['TCA']['tt_content']['types'][$cType] = [];
    }
    $GLOBALS['TCA']['tt_content']['types'][$cType]['showitem'] = $tab1WithoutFlexForm . $sharedTabs;
}
