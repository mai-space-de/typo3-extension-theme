<?php

declare(strict_types=1);

defined("TYPO3") or die();

use Maispace\MaiBase\TableConfigurationArray\CType;
use Maispace\MaiBase\TableConfigurationArray\Field;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$lang = Helper::localLangHelperFactory(
    "mai_theme",
    "Default/locallang_tca.xlf",
);

// ============================================================================
// SHARED FIELDS ON tt_content
// ============================================================================

// ── Appearance ────────────────────────────────────────────────────────────────

new Field("tt_content", "tx_maitheme_bg_color", $lang("field.bg_color"))
    ->setConfig(["type" => "input", "size" => 12, "eval" => "trim"])
    ->registerField();

new Field("tt_content", "tx_maitheme_bg_image", $lang("field.bg_image"))
    ->setConfig([
        "type" => "file",
        "allowed" => "common-image-types",
        "maxitems" => 1,
        "appearance" => [
            "createNewRelationLinkTitle" => "Add background image",
        ],
    ])
    ->registerField();

new Field("tt_content", "tx_maitheme_text_color", $lang("field.text_color"))
    ->setConfig(["type" => "input", "size" => 12, "eval" => "trim"])
    ->registerField();

new Field(
    "tt_content",
    "tx_maitheme_border_radius",
    $lang("field.border_radius"),
)
    ->setConfig(["type" => "input", "size" => 12, "eval" => "trim"])
    ->registerField();

new Field("tt_content", "tx_maitheme_shadow", $lang("field.shadow"))
    ->setConfig([
        "type" => "select",
        "renderType" => "selectSingle",
        "items" => [
            ["label" => "", "value" => ""],
            ["label" => $lang("shadow.sm"), "value" => "sm"],
            ["label" => $lang("shadow.md"), "value" => "md"],
            ["label" => $lang("shadow.lg"), "value" => "lg"],
            ["label" => $lang("shadow.none"), "value" => "none"],
        ],
    ])
    ->registerField();

new Field("tt_content", "tx_maitheme_opacity", $lang("field.opacity"))
    ->setConfig([
        "type" => "number",
        "format" => "decimal",
        "range" => ["lower" => 0, "upper" => 1],
        "default" => 1,
    ])
    ->registerField();

// ── Spacing ───────────────────────────────────────────────────────────────────

$spacingItems = [
    ["label" => $lang("spacing.inherit"), "value" => ""],
    ["label" => $lang("spacing.none"), "value" => "none"],
    ["label" => $lang("spacing.2xs"), "value" => "2xs"],
    ["label" => $lang("spacing.xs"), "value" => "xs"],
    ["label" => $lang("spacing.sm"), "value" => "sm"],
    ["label" => $lang("spacing.md"), "value" => "md"],
    ["label" => $lang("spacing.lg"), "value" => "lg"],
    ["label" => $lang("spacing.xl"), "value" => "xl"],
    ["label" => $lang("spacing.2xl"), "value" => "2xl"],
    ["label" => $lang("spacing.3xl"), "value" => "3xl"],
];

new Field("tt_content", "tx_maitheme_margin_top", $lang("field.margin_top"))
    ->setConfig([
        "type" => "select",
        "renderType" => "selectSingle",
        "items" => $spacingItems,
    ])
    ->registerField();

new Field(
    "tt_content",
    "tx_maitheme_margin_bottom",
    $lang("field.margin_bottom"),
)
    ->setConfig([
        "type" => "select",
        "renderType" => "selectSingle",
        "items" => $spacingItems,
    ])
    ->registerField();

new Field("tt_content", "tx_maitheme_padding_top", $lang("field.padding_top"))
    ->setConfig([
        "type" => "select",
        "renderType" => "selectSingle",
        "items" => $spacingItems,
    ])
    ->registerField();

new Field(
    "tt_content",
    "tx_maitheme_padding_bottom",
    $lang("field.padding_bottom"),
)
    ->setConfig([
        "type" => "select",
        "renderType" => "selectSingle",
        "items" => $spacingItems,
    ])
    ->registerField();

new Field("tt_content", "tx_maitheme_max_width", $lang("field.max_width"))
    ->setConfig([
        "type" => "select",
        "renderType" => "selectSingle",
        "items" => [
            ["label" => "", "value" => ""],
            ["label" => $lang("maxwidth.full"), "value" => "full"],
            ["label" => $lang("maxwidth.content"), "value" => "content"],
            ["label" => $lang("maxwidth.text"), "value" => "text"],
            ["label" => $lang("maxwidth.card"), "value" => "card"],
        ],
    ])
    ->registerField();

new Field("tt_content", "tx_maitheme_alignment", $lang("field.alignment"))
    ->setConfig([
        "type" => "select",
        "renderType" => "selectSingle",
        "items" => [
            ["label" => "", "value" => ""],
            ["label" => $lang("alignment.left"), "value" => "left"],
            ["label" => $lang("alignment.center"), "value" => "center"],
            ["label" => $lang("alignment.right"), "value" => "right"],
        ],
    ])
    ->registerField();

// ── Animation ─────────────────────────────────────────────────────────────────

new Field("tt_content", "tx_maitheme_animation", $lang("field.animation"))
    ->setConfig([
        "type" => "select",
        "renderType" => "selectSingle",
        "items" => [
            ["label" => $lang("animation.none"), "value" => ""],
            ["label" => $lang("animation.fade"), "value" => "fade"],
            ["label" => $lang("animation.slide"), "value" => "slide"],
            ["label" => $lang("animation.zoom"), "value" => "zoom"],
        ],
    ])
    ->registerField();

new Field(
    "tt_content",
    "tx_maitheme_animation_delay",
    $lang("field.animation_delay"),
)
    ->setConfig([
        "type" => "number",
        "format" => "integer",
        "range" => ["lower" => 0, "upper" => 2000],
        "default" => 0,
        "size" => 6,
    ])
    ->registerField();

// ── Linking ───────────────────────────────────────────────────────────────────

new Field("tt_content", "tx_maitheme_link", $lang("field.link"))
    ->setConfig(["type" => "link"])
    ->registerField();

new Field("tt_content", "tx_maitheme_anchor_id", $lang("field.anchor_id"))
    ->setConfig(["type" => "input", "size" => 30, "eval" => "trim"])
    ->registerField();

new Field("tt_content", "tx_maitheme_aria_label", $lang("field.aria_label"))
    ->setConfig(["type" => "input", "size" => 50, "eval" => "trim"])
    ->registerField();

// ── Responsive ────────────────────────────────────────────────────────────────

new Field("tt_content", "tx_maitheme_hide_desktop", $lang("field.hide_desktop"))
    ->setConfig(["type" => "check", "default" => 0])
    ->registerField();

new Field("tt_content", "tx_maitheme_hide_tablet", $lang("field.hide_tablet"))
    ->setConfig(["type" => "check", "default" => 0])
    ->registerField();

new Field("tt_content", "tx_maitheme_hide_mobile", $lang("field.hide_mobile"))
    ->setConfig(["type" => "check", "default" => 0])
    ->registerField();

new Field("tt_content", "tx_maitheme_col_span", $lang("field.col_span"))
    ->setConfig([
        "type" => "select",
        "renderType" => "selectSingle",
        "items" => [
            ["label" => $lang("colspan.auto"), "value" => ""],
            ["label" => "1/4  (3/12)", "value" => "3"],
            ["label" => "1/3  (4/12)", "value" => "4"],
            ["label" => "5/12", "value" => "5"],
            ["label" => "1/2  (6/12)", "value" => "6"],
            ["label" => "7/12", "value" => "7"],
            ["label" => "2/3  (8/12)", "value" => "8"],
            ["label" => "3/4  (9/12)", "value" => "9"],
            ["label" => "Full (12/12)", "value" => "12"],
        ],
    ])
    ->registerField();

// ── Advanced ──────────────────────────────────────────────────────────────────

new Field("tt_content", "tx_maitheme_custom_class", $lang("field.custom_class"))
    ->setConfig(["type" => "input", "size" => 60, "eval" => "trim"])
    ->registerField();

new Field("tt_content", "tx_maitheme_custom_id", $lang("field.custom_id"))
    ->setConfig(["type" => "input", "size" => 30, "eval" => "trim"])
    ->registerField();

new Field("tt_content", "tx_maitheme_data_attrs", $lang("field.data_attrs"))
    ->setConfig(["type" => "text", "rows" => 3, "cols" => 60])
    ->registerField();

// ============================================================================
// SHARED PALETTES (registered once, referenced by all CTypes)
// ============================================================================

ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_appearance",
    'tx_maitheme_bg_color, tx_maitheme_bg_image, --linebreak--,
     tx_maitheme_text_color, tx_maitheme_border_radius, tx_maitheme_shadow, tx_maitheme_opacity',
);

ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_spacing",
    'tx_maitheme_margin_top, tx_maitheme_margin_bottom, --linebreak--,
     tx_maitheme_padding_top, tx_maitheme_padding_bottom, --linebreak--,
     tx_maitheme_max_width, tx_maitheme_alignment',
);

ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_animation",
    "tx_maitheme_animation, tx_maitheme_animation_delay",
);

ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_linking",
    "tx_maitheme_link, tx_maitheme_anchor_id, tx_maitheme_aria_label",
);

ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_responsive",
    'tx_maitheme_hide_desktop, tx_maitheme_hide_tablet, tx_maitheme_hide_mobile,
     --linebreak--, tx_maitheme_col_span',
);

ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_advanced",
    "tx_maitheme_custom_class, tx_maitheme_custom_id, --linebreak--, tx_maitheme_data_attrs",
);

// ============================================================================
// SHARED TABS STRING (appended to every CType after element-specific fields)
// ============================================================================

$sharedTabs =
    "--div--;" .
    $lang("tab.appearance") .
    "," .
    "--palette--;;maispace_appearance," .
    "--palette--;;maispace_spacing," .
    "--div--;" .
    $lang("tab.animation") .
    "," .
    "--palette--;;maispace_animation," .
    "--palette--;;maispace_linking," .
    "--palette--;;maispace_responsive," .
    "--div--;" .
    $lang("tab.advanced") .
    "," .
    "--palette--;;maispace_advanced";

// ============================================================================
// CTYPE ITEM GROUPS
// Declares the group labels for the CType selector in the record editing mask.
// Each maispace_* group key must match the 'group' value passed when
// registering CTypes via Helper::addCType() / CType::setGroup().
// ============================================================================

$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'] = array_merge(
    $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'] ?? [],
    [
        'maispace_basic'      => $lang('wizard.basic'),
        'maispace_page'       => $lang('wizard.page'),
        'maispace_components' => $lang('wizard.components'),
        'maispace_widgets'    => $lang('wizard.widgets'),
        'maispace_forms'      => $lang('wizard.forms'),
        'maispace_data'       => $lang('wizard.data'),
        'maispace_feedback'   => $lang('wizard.feedback'),
        'maispace_sections'   => $lang('wizard.sections'),
    ]
);

// ============================================================================
// BASIC ELEMENTS (10)
// text, heading, richtext, image, video, audio, button, linklist, icon, divider
// ============================================================================

new CType("maispace_text", $lang("ctype.text"), "content-text")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_heading", $lang("ctype.heading"), "content-header")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_richtext", $lang("ctype.richtext"), "content-text-mixed")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_image", $lang("ctype.image"), "content-image")
    ->addDefaultHeaderPalette()
    ->addDefaultImageTab()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_video", $lang("ctype.video"), "content-media")
    ->addDefaultHeaderPalette()
    ->addDefaultMediaTab()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_audio", $lang("ctype.audio"), "content-media")
    ->addDefaultHeaderPalette()
    ->addDefaultMediaTab()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_button", $lang("ctype.button"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addCustomFields("tx_maitheme_link")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_linklist", $lang("ctype.linklist"), "content-bullets")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_icon", $lang("ctype.icon"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_divider", $lang("ctype.divider"), "content-special-div")
    ->disableGeneralPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

// ============================================================================
// PAGE SECTIONS (4)
// hero, banner, cta, mediatext
// ============================================================================

new CType("maispace_hero", $lang("ctype.hero"), "content-textmedia")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_page')
    ->register();

new CType("maispace_banner", $lang("ctype.banner"), "content-image")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext, image, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_page')
    ->register();

new CType("maispace_cta", $lang("ctype.cta"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_page')
    ->register();

new CType("maispace_mediatext", $lang("ctype.mediatext"), "content-textmedia")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addDefaultImageTab()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_page')
    ->register();

// ============================================================================
// CARDS & COMPONENTS (13)
// card, teaser, featurebox, profile, testimonial, quote, logo, logoshowcase,
// statistic, pricebox, badge, event, jobposting
// ============================================================================

new CType("maispace_card", $lang("ctype.card"), "content-textmedia")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_teaser", $lang("ctype.teaser"), "content-textmedia")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_featurebox", $lang("ctype.featurebox"), "content-textmedia")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_profile", $lang("ctype.profile"), "content-image")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_testimonial", $lang("ctype.testimonial"), "content-quote")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_quote", $lang("ctype.quote"), "content-quote")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_logo", $lang("ctype.logo"), "content-image")
    ->addCustomFields("image, tx_maitheme_link")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_logoshowcase", $lang("ctype.logoshowcase"), "content-image")
    ->addDefaultHeaderPalette()
    ->addDefaultImageTab()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_statistic", $lang("ctype.statistic"), "content-table")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_pricebox", $lang("ctype.pricebox"), "content-table")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_badge", $lang("ctype.badge"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_event", $lang("ctype.event"), "content-table")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_jobposting", $lang("ctype.jobposting"), "content-table")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

// ============================================================================
// UI WIDGETS (6)
// slider, accordion, tabs, modal, timeline, faq
// ============================================================================

new CType("maispace_slider", $lang("ctype.slider"), "content-carousel")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_accordion", $lang("ctype.accordion"), "content-accordion")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_tabs", $lang("ctype.tabs"), "content-tab")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_modal", $lang("ctype.modal"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 1]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_timeline", $lang("ctype.timeline"), "content-list-bullet")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_faq", $lang("ctype.faq"), "content-accordion")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

// ============================================================================
// FORMS & ENGAGEMENT (4)
// form, search, newsletter, map
// ============================================================================

new CType("maispace_form", $lang("ctype.form"), "content-form")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_forms')
    ->register();

new CType("maispace_search", $lang("ctype.search"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_forms')
    ->register();

new CType("maispace_newsletter", $lang("ctype.newsletter"), "content-form")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_forms')
    ->register();

new CType("maispace_map", $lang("ctype.map"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_forms')
    ->register();

// ============================================================================
// DATA & MEDIA (8)
// table, datalist, gallery, filelist, chart, codeblock, progressbar, rating
// ============================================================================

new CType("maispace_table", $lang("ctype.table"), "content-table")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", [
        "config" => ["renderType" => "textTable", "wrap" => "off"],
    ])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType("maispace_datalist", $lang("ctype.datalist"), "content-table")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType("maispace_gallery", $lang("ctype.gallery"), "content-image-gallery")
    ->addDefaultHeaderPalette()
    ->addDefaultImageTab()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType("maispace_filelist", $lang("ctype.filelist"), "content-filelinks")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType(
    "maispace_codeblock",
    $lang("ctype.codeblock"),
    "content-special-html",
)
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", [
        "config" => [
            "format" => "html",
            "renderType" => "codeEditor",
            "wrap" => "off",
        ],
    ])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType("maispace_chart", $lang("ctype.chart"), "content-table")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType(
    "maispace_progressbar",
    $lang("ctype.progressbar"),
    "content-special-html",
)
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType("maispace_rating", $lang("ctype.rating"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

// ============================================================================
// FEEDBACK & STATE (5)
// alert, notification, spinner, emptystate, confirmation
// ============================================================================

new CType("maispace_alert", $lang("ctype.alert"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_feedback')
    ->register();

new CType(
    "maispace_notification",
    $lang("ctype.notification"),
    "content-special-html",
)
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_feedback')
    ->register();

new CType("maispace_spinner", $lang("ctype.spinner"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_feedback')
    ->register();

new CType(
    "maispace_emptystate",
    $lang("ctype.emptystate"),
    "content-special-html",
)
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_feedback')
    ->register();

new CType(
    "maispace_confirmation",
    $lang("ctype.confirmation"),
    "content-special-html",
)
    ->addDefaultHeaderPalette()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => 0]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_feedback')
    ->register();

// ============================================================================
// SECTION CONTAINERS (b13/container)
// colPos range 200–299 — no conflict with core layout colPos 0–6
// ============================================================================

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded("container")) {
    $lll =
        "LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:";

    $containerRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \B13\Container\Tca\Registry::class,
    );

    $containerRegistry->configureContainer(
        new \B13\Container\Tca\ContainerConfiguration(
            "maispace_section_full",
            $lll . "ctype.section_full",
            "",
            [
                [
                    [
                        "name" => $lll . "col.content",
                        "colPos" => 250,
                        "colspan" => 12,
                    ],
                ],
            ],
        )->setIcon("content-container-columns-1"),
    );

    $containerRegistry->configureContainer(
        new \B13\Container\Tca\ContainerConfiguration(
            "maispace_section_50_50",
            $lll . "ctype.section_50_50",
            "",
            [
                [
                    [
                        "name" => $lll . "col.left",
                        "colPos" => 200,
                        "colspan" => 6,
                    ],
                    [
                        "name" => $lll . "col.right",
                        "colPos" => 201,
                        "colspan" => 6,
                    ],
                ],
            ],
        )->setIcon("content-container-columns-2"),
    );

    $containerRegistry->configureContainer(
        new \B13\Container\Tca\ContainerConfiguration(
            "maispace_section_66_33",
            $lll . "ctype.section_66_33",
            "",
            [
                [
                    [
                        "name" => $lll . "col.wide",
                        "colPos" => 210,
                        "colspan" => 8,
                    ],
                    [
                        "name" => $lll . "col.narrow",
                        "colPos" => 211,
                        "colspan" => 4,
                    ],
                ],
            ],
        )->setIcon("content-container-columns-2"),
    );

    $containerRegistry->configureContainer(
        new \B13\Container\Tca\ContainerConfiguration(
            "maispace_section_33_66",
            $lll . "ctype.section_33_66",
            "",
            [
                [
                    [
                        "name" => $lll . "col.narrow",
                        "colPos" => 220,
                        "colspan" => 4,
                    ],
                    [
                        "name" => $lll . "col.wide",
                        "colPos" => 221,
                        "colspan" => 8,
                    ],
                ],
            ],
        )->setIcon("content-container-columns-2"),
    );

    $containerRegistry->configureContainer(
        new \B13\Container\Tca\ContainerConfiguration(
            "maispace_section_3col",
            $lll . "ctype.section_3col",
            "",
            [
                [
                    [
                        "name" => $lll . "col.col1",
                        "colPos" => 230,
                        "colspan" => 4,
                    ],
                    [
                        "name" => $lll . "col.col2",
                        "colPos" => 231,
                        "colspan" => 4,
                    ],
                    [
                        "name" => $lll . "col.col3",
                        "colPos" => 232,
                        "colspan" => 4,
                    ],
                ],
            ],
        )->setIcon("content-container-columns-3"),
    );

    $containerRegistry->configureContainer(
        new \B13\Container\Tca\ContainerConfiguration(
            "maispace_section_4col",
            $lll . "ctype.section_4col",
            "",
            [
                [
                    [
                        "name" => $lll . "col.col1",
                        "colPos" => 240,
                        "colspan" => 3,
                    ],
                    [
                        "name" => $lll . "col.col2",
                        "colPos" => 241,
                        "colspan" => 3,
                    ],
                    [
                        "name" => $lll . "col.col3",
                        "colPos" => 242,
                        "colspan" => 3,
                    ],
                    [
                        "name" => $lll . "col.col4",
                        "colPos" => 243,
                        "colspan" => 3,
                    ],
                ],
            ],
        )->setIcon("content-container-columns-4"),
    );

    $containerRegistry->configureContainer(
        new \B13\Container\Tca\ContainerConfiguration(
            "maispace_section_sidebar_r",
            $lll . "ctype.section_sidebar_r",
            "",
            [
                [
                    [
                        "name" => $lll . "col.main",
                        "colPos" => 260,
                        "colspan" => 9,
                    ],
                    [
                        "name" => $lll . "col.sidebar",
                        "colPos" => 261,
                        "colspan" => 3,
                    ],
                ],
            ],
        )->setIcon("content-container-columns-2"),
    );

    $containerRegistry->configureContainer(
        new \B13\Container\Tca\ContainerConfiguration(
            "maispace_section_sidebar_l",
            $lll . "ctype.section_sidebar_l",
            "",
            [
                [
                    [
                        "name" => $lll . "col.sidebar",
                        "colPos" => 270,
                        "colspan" => 3,
                    ],
                    [
                        "name" => $lll . "col.main",
                        "colPos" => 271,
                        "colspan" => 9,
                    ],
                ],
            ],
        )->setIcon("content-container-columns-2"),
    );

    // Assign maispace_sections group to all section container CType items.
    // configureContainer() does not support a 'group' key, so we patch the
    // TCA items array directly after all containers have been registered.
    foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as &$item) {
        if (isset($item['value']) && str_starts_with($item['value'], 'maispace_section_')) {
            $item['group'] = 'maispace_sections';
        }
    }
    unset($item);
}
