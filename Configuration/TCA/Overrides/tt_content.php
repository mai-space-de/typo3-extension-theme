<?php

declare(strict_types=1);

defined("TYPO3") or die();

use Maispace\MaiBase\TableConfigurationArray\CType;
use Maispace\MaiBase\TableConfigurationArray\Field;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\CheckboxConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\EmailConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\FileConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\InputConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\LinkConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\NumberConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\SelectSingleConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\TextConfig;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$lang = Helper::localLangHelperFactory(
    "mai_theme",
    "Default/locallang_tca.xlf",
);

// ============================================================================
// SHARED FIELDS ON tt_content
// ============================================================================

// ── Design (formerly Appearance) ─────────────────────────────────────────────

$colorTokenItems = [
    ["label" => $lang("color.inherit"), "value" => ""],
    ["label" => $lang("color.primary"), "value" => "primary"],
    ["label" => $lang("color.secondary"), "value" => "secondary"],
    ["label" => $lang("color.surface"), "value" => "surface"],
    ["label" => $lang("color.surface_alt"), "value" => "surface-alt"],
    ["label" => $lang("color.accent"), "value" => "accent"],
    ["label" => $lang("color.muted"), "value" => "muted"],
    ["label" => $lang("color.dark"), "value" => "dark"],
    ["label" => $lang("color.light"), "value" => "light"],
    ["label" => $lang("color.danger"), "value" => "danger"],
    ["label" => $lang("color.success"), "value" => "success"],
    ["label" => $lang("color.warning"), "value" => "warning"],
    ["label" => $lang("color.info"), "value" => "info"],
];

new Field("tt_content", "tx_maitheme_bg_color", $lang("field.bg_color"))
    ->setConfig((new SelectSingleConfig())->setItems($colorTokenItems))
    ->registerField();

new Field("tt_content", "tx_maitheme_bg_image", $lang("field.bg_image"))
    ->setConfig(
        (new FileConfig())
            ->setAllowed("common-image-types")
            ->setMaxItems(1)
            ->setAppearance(["createNewRelationLinkTitle" => "Add background image"])
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_text_color", $lang("field.text_color"))
    ->setConfig((new SelectSingleConfig())->setItems($colorTokenItems))
    ->registerField();

new Field(
    "tt_content",
    "tx_maitheme_border_radius",
    $lang("field.border_radius"),
)
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("radius.inherit"), "value" => ""],
            ["label" => $lang("radius.none"), "value" => "none"],
            ["label" => $lang("radius.sm"), "value" => "sm"],
            ["label" => $lang("radius.md"), "value" => "md"],
            ["label" => $lang("radius.lg"), "value" => "lg"],
            ["label" => $lang("radius.full"), "value" => "full"],
        ])
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_shadow", $lang("field.shadow"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => "", "value" => ""],
            ["label" => $lang("shadow.sm"), "value" => "sm"],
            ["label" => $lang("shadow.md"), "value" => "md"],
            ["label" => $lang("shadow.lg"), "value" => "lg"],
            ["label" => $lang("shadow.none"), "value" => "none"],
        ])
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_opacity", $lang("field.opacity"))
    ->setConfig((new NumberConfig())->setFormat("decimal")->setRange(0, 1)->setDefault(1))
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
    ->setConfig((new SelectSingleConfig())->setItems($spacingItems))
    ->registerField();

new Field(
    "tt_content",
    "tx_maitheme_margin_bottom",
    $lang("field.margin_bottom"),
)
    ->setConfig((new SelectSingleConfig())->setItems($spacingItems))
    ->registerField();

new Field("tt_content", "tx_maitheme_padding_top", $lang("field.padding_top"))
    ->setConfig((new SelectSingleConfig())->setItems($spacingItems))
    ->registerField();

new Field(
    "tt_content",
    "tx_maitheme_padding_bottom",
    $lang("field.padding_bottom"),
)
    ->setConfig((new SelectSingleConfig())->setItems($spacingItems))
    ->registerField();

new Field("tt_content", "tx_maitheme_max_width", $lang("field.max_width"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => "", "value" => ""],
            ["label" => $lang("maxwidth.full"), "value" => "full"],
            ["label" => $lang("maxwidth.content"), "value" => "content"],
            ["label" => $lang("maxwidth.text"), "value" => "text"],
            ["label" => $lang("maxwidth.card"), "value" => "card"],
        ])
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_alignment", $lang("field.alignment"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => "", "value" => ""],
            ["label" => $lang("alignment.left"), "value" => "left"],
            ["label" => $lang("alignment.center"), "value" => "center"],
            ["label" => $lang("alignment.right"), "value" => "right"],
        ])
    )
    ->registerField();

// ── Responsive ────────────────────────────────────────────────────────────────

new Field("tt_content", "tx_maitheme_hide_desktop", $lang("field.hide_desktop"))
    ->setConfig((new CheckboxConfig())->setDefault(0))
    ->registerField();

new Field("tt_content", "tx_maitheme_hide_tablet", $lang("field.hide_tablet"))
    ->setConfig((new CheckboxConfig())->setDefault(0))
    ->registerField();

new Field("tt_content", "tx_maitheme_hide_mobile", $lang("field.hide_mobile"))
    ->setConfig((new CheckboxConfig())->setDefault(0))
    ->registerField();

new Field("tt_content", "tx_maitheme_col_span", $lang("field.col_span"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("colspan.auto"), "value" => ""],
            ["label" => "1/4  (3/12)", "value" => "3"],
            ["label" => "1/3  (4/12)", "value" => "4"],
            ["label" => "5/12", "value" => "5"],
            ["label" => "1/2  (6/12)", "value" => "6"],
            ["label" => "7/12", "value" => "7"],
            ["label" => "2/3  (8/12)", "value" => "8"],
            ["label" => "3/4  (9/12)", "value" => "9"],
            ["label" => "Full (12/12)", "value" => "12"],
        ])
    )
    ->registerField();

// ── Animation ─────────────────────────────────────────────────────────────────

new Field("tt_content", "tx_maitheme_animation", $lang("field.animation"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("animation.none"), "value" => ""],
            ["label" => $lang("animation.fade"), "value" => "fade"],
            ["label" => $lang("animation.slide"), "value" => "slide"],
            ["label" => $lang("animation.zoom"), "value" => "zoom"],
        ])
    )
    ->registerField();

new Field(
    "tt_content",
    "tx_maitheme_animation_delay",
    $lang("field.animation_delay"),
)
    ->setConfig(
        (new NumberConfig())->setFormat("integer")->setRange(0, 2000)->setDefault(0)->setSize(6)
    )
    ->registerField();

// ── Linking (anchor / accessibility only — tx_maitheme_link goes per-CType) ──

new Field("tt_content", "tx_maitheme_link", $lang("field.link"))
    ->setConfig(new LinkConfig())
    ->registerField();

new Field("tt_content", "tx_maitheme_link_text", $lang("field.link_text"))
    ->setConfig((new InputConfig())->setSize(40)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_link_secondary", $lang("field.link_secondary"))
    ->setConfig(new LinkConfig())
    ->registerField();

new Field("tt_content", "tx_maitheme_link_secondary_text", $lang("field.link_secondary_text"))
    ->setConfig((new InputConfig())->setSize(40)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_eyebrow", $lang("field.eyebrow"))
    ->setConfig((new InputConfig())->setSize(50)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_badge_text", $lang("field.badge_text"))
    ->setConfig((new InputConfig())->setSize(40)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_anchor_id", $lang("field.anchor_id"))
    ->setConfig((new InputConfig())->setSize(30)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_aria_label", $lang("field.aria_label"))
    ->setConfig((new InputConfig())->setSize(50)->setEval("trim"))
    ->registerField();

// ── Advanced ──────────────────────────────────────────────────────────────────

new Field("tt_content", "tx_maitheme_custom_class", $lang("field.custom_class"))
    ->setConfig((new InputConfig())->setSize(60)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_custom_id", $lang("field.custom_id"))
    ->setConfig((new InputConfig())->setSize(30)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_data_attrs", $lang("field.data_attrs"))
    ->setConfig((new TextConfig())->setRows(3)->setCols(60))
    ->registerField();

// ── Shared variant (alert style for alert, notification, badge) ───────────────

new Field("tt_content", "tx_maitheme_variant", $lang("field.variant"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("variant.default"), "value" => ""],
            ["label" => $lang("variant.info"), "value" => "info"],
            ["label" => $lang("variant.success"), "value" => "success"],
            ["label" => $lang("variant.warning"), "value" => "warning"],
            ["label" => $lang("variant.danger"), "value" => "danger"],
        ])
    )
    ->registerField();

// ── Brand accent (teal/gold card border — offer-card family) ─────────────────

new Field("tt_content", "tx_maitheme_accent", $lang("field.accent"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("accent.teal"), "value" => ""],
            ["label" => $lang("accent.gold"), "value" => "gold"],
        ])
    )
    ->registerField();

// ── Per-CType specific fields ─────────────────────────────────────────────────

// Icon element
new Field("tt_content", "tx_maitheme_icon_identifier", $lang("field.icon_identifier"))
    ->setConfig((new InputConfig())->setSize(40)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_icon_size", $lang("field.icon_size"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("iconsize.md"), "value" => ""],
            ["label" => $lang("iconsize.sm"), "value" => "sm"],
            ["label" => $lang("iconsize.lg"), "value" => "lg"],
            ["label" => $lang("iconsize.xl"), "value" => "xl"],
            ["label" => $lang("iconsize.2xl"), "value" => "2xl"],
        ])
    )
    ->registerField();

// Divider element
new Field("tt_content", "tx_maitheme_divider_style", $lang("field.divider_style"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("dividerstyle.line"), "value" => ""],
            ["label" => $lang("dividerstyle.dashed"), "value" => "dashed"],
            ["label" => $lang("dividerstyle.dotted"), "value" => "dotted"],
            ["label" => $lang("dividerstyle.gradient"), "value" => "gradient"],
            ["label" => $lang("dividerstyle.blank"), "value" => "blank"],
        ])
    )
    ->registerField();

// Button element
new Field("tt_content", "tx_maitheme_button_style", $lang("field.button_style"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("buttonstyle.primary"), "value" => ""],
            ["label" => $lang("buttonstyle.secondary"), "value" => "secondary"],
            ["label" => $lang("buttonstyle.ghost"), "value" => "ghost"],
            ["label" => $lang("buttonstyle.outline"), "value" => "outline"],
            ["label" => $lang("buttonstyle.link"), "value" => "link"],
        ])
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_button_size", $lang("field.button_size"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("buttonsize.md"), "value" => ""],
            ["label" => $lang("buttonsize.sm"), "value" => "sm"],
            ["label" => $lang("buttonsize.lg"), "value" => "lg"],
        ])
    )
    ->registerField();

// Hero / Banner element
new Field("tt_content", "tx_maitheme_content_position", $lang("field.content_position"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("contentposition.left"), "value" => ""],
            ["label" => $lang("contentposition.center"), "value" => "center"],
            ["label" => $lang("contentposition.right"), "value" => "right"],
        ])
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_overlay_style", $lang("field.overlay_style"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("overlay.none"), "value" => ""],
            ["label" => $lang("overlay.light"), "value" => "light"],
            ["label" => $lang("overlay.dark"), "value" => "dark"],
            ["label" => $lang("overlay.gradient"), "value" => "gradient"],
        ])
    )
    ->registerField();

// Media & Text element
new Field("tt_content", "tx_maitheme_media_position", $lang("field.media_position"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("mediaposition.left"), "value" => ""],
            ["label" => $lang("mediaposition.right"), "value" => "right"],
        ])
    )
    ->registerField();

// Statistic element
new Field("tt_content", "tx_maitheme_unit", $lang("field.unit"))
    ->setConfig((new InputConfig())->setSize(10)->setEval("trim"))
    ->registerField();

// Progressbar element
new Field("tt_content", "tx_maitheme_value", $lang("field.value"))
    ->setConfig(
        (new NumberConfig())->setFormat("integer")->setRange(0, 100)->setDefault(0)->setSize(4)
    )
    ->registerField();

// Map element
new Field("tt_content", "tx_maitheme_lat", $lang("field.lat"))
    ->setConfig((new InputConfig())->setSize(20)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_lng", $lang("field.lng"))
    ->setConfig((new InputConfig())->setSize(20)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_zoom", $lang("field.zoom"))
    ->setConfig(
        (new NumberConfig())->setFormat("integer")->setRange(1, 20)->setDefault(14)->setSize(3)
    )
    ->registerField();

// Code Block element
new Field("tt_content", "tx_maitheme_language", $lang("field.language"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("codelang.auto"), "value" => ""],
            ["label" => "HTML", "value" => "html"],
            ["label" => "CSS", "value" => "css"],
            ["label" => "JavaScript", "value" => "js"],
            ["label" => "TypeScript", "value" => "ts"],
            ["label" => "PHP", "value" => "php"],
            ["label" => "JSON", "value" => "json"],
            ["label" => "Bash / Shell", "value" => "bash"],
            ["label" => "SQL", "value" => "sql"],
            ["label" => "YAML", "value" => "yaml"],
            ["label" => "Markdown", "value" => "markdown"],
        ])
    )
    ->registerField();

// Rating element
new Field("tt_content", "tx_maitheme_rating_max", $lang("field.rating_max"))
    ->setConfig(
        (new NumberConfig())->setFormat("integer")->setRange(1, 10)->setDefault(5)->setSize(3)
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_rating_value", $lang("field.rating_value"))
    ->setConfig(
        (new NumberConfig())->setFormat("decimal")->setRange(0, 10)->setDefault(0)->setSize(4)
    )
    ->registerField();

// ── Video provider fields (mai:video.video — YouTube / Vimeo facade + background) ──
new Field("tt_content", "tx_maitheme_video_youtube_id", $lang("field.video_youtube_id"))
    ->setConfig((new InputConfig())->setSize(20)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_video_vimeo_id", $lang("field.video_vimeo_id"))
    ->setConfig((new InputConfig())->setSize(20)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_video_poster", $lang("field.video_poster"))
    ->setConfig(
        (new FileConfig())
            ->setAllowed("common-image-types")
            ->setMaxItems(1)
            ->setAppearance(["createNewRelationLinkTitle" => "Add poster image"])
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_video_type", $lang("field.video_type"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("videotype.content"), "value" => "content"],
            ["label" => $lang("videotype.background"), "value" => "background"],
        ])
    )
    ->registerField();

// ── Image display fields (mai:image.responsiveImage / .picture / .figure) ────
new Field("tt_content", "tx_maitheme_image_ratio", $lang("field.image_ratio"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("imageratio.auto"), "value" => ""],
            ["label" => $lang("imageratio.square"), "value" => "1-1"],
            ["label" => $lang("imageratio.16_9"), "value" => "16-9"],
            ["label" => $lang("imageratio.4_3"), "value" => "4-3"],
            ["label" => $lang("imageratio.3_4"), "value" => "3-4"],
            ["label" => $lang("imageratio.21_9"), "value" => "21-9"],
        ])
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_image_fit", $lang("field.image_fit"))
    ->setConfig(
        (new SelectSingleConfig())->setItems([
            ["label" => $lang("imagefit.cover"), "value" => ""],
            ["label" => $lang("imagefit.contain"), "value" => "contain"],
            ["label" => $lang("imagefit.fill"), "value" => "fill"],
            ["label" => $lang("imagefit.scaledown"), "value" => "scale-down"],
        ])
    )
    ->registerField();

// ── Before/After Slider element ───────────────────────────────────────────────
new Field("tt_content", "tx_maitheme_image_before", $lang("field.image_before"))
    ->setConfig(
        (new FileConfig())
            ->setAllowed("common-image-types")
            ->setMaxItems(1)
            ->setAppearance(["createNewRelationLinkTitle" => "Add before image"])
    )
    ->registerField();

new Field("tt_content", "tx_maitheme_image_after", $lang("field.image_after"))
    ->setConfig(
        (new FileConfig())
            ->setAllowed("common-image-types")
            ->setMaxItems(1)
            ->setAppearance(["createNewRelationLinkTitle" => "Add after image"])
    )
    ->registerField();

// ── Contact Info element ──────────────────────────────────────────────────────
new Field("tt_content", "tx_maitheme_address", $lang("field.address"))
    ->setConfig((new TextConfig())->setRows(3)->setCols(40))
    ->registerField();

new Field("tt_content", "tx_maitheme_phone", $lang("field.phone"))
    ->setConfig((new InputConfig())->setSize(30)->setEval("trim"))
    ->registerField();

new Field("tt_content", "tx_maitheme_email", $lang("field.email"))
    ->setConfig((new EmailConfig())->setSize(40))
    ->registerField();

new Field("tt_content", "tx_maitheme_opening_hours", $lang("field.opening_hours"))
    ->setConfig((new TextConfig())->setRows(4)->setCols(40))
    ->registerField();

// ============================================================================
// SHARED PALETTES (registered once, referenced by all CTypes)
// ============================================================================

ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_design",
    'tx_maitheme_bg_color, tx_maitheme_text_color, --linebreak--,
     tx_maitheme_bg_image, --linebreak--,
     tx_maitheme_border_radius, tx_maitheme_shadow, tx_maitheme_opacity',
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
    "maispace_responsive",
    'tx_maitheme_hide_desktop, tx_maitheme_hide_tablet, tx_maitheme_hide_mobile,
     --linebreak--, tx_maitheme_col_span',
);

ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_animation",
    "tx_maitheme_animation, tx_maitheme_animation_delay",
);

// anchor_id and aria_label only — tx_maitheme_link is placed per-CType in Tab 1
ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_linking",
    "tx_maitheme_anchor_id, tx_maitheme_aria_label",
);

ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_advanced",
    "tx_maitheme_custom_class, tx_maitheme_custom_id, --linebreak--, tx_maitheme_data_attrs",
);

// Video provider palette — consumed by mai:video.video ViewHelper
// (self-hosted file lives in core `assets`; this palette adds YouTube/Vimeo/poster/type).
ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_video_providers",
    "tx_maitheme_video_type, --linebreak--,
     tx_maitheme_video_youtube_id, tx_maitheme_video_vimeo_id, --linebreak--,
     tx_maitheme_video_poster",
);

// Image display palette — consumed by mai:image.* ViewHelpers via --local-img-* CSS vars
ExtensionManagementUtility::addFieldsToPalette(
    "tt_content",
    "maispace_image_display",
    "tx_maitheme_image_ratio, tx_maitheme_image_fit",
);

// ============================================================================
// SHARED TABS STRING (appended to every CType after element-specific fields)
// Tab 1 "General" is the TYPO3 core tab (element content lives there).
// Tab 2 "Appearance & Layout" — design tokens + spacing + responsive.
// Tab 3 "Advanced"            — animation + anchor/aria + developer fields.
// Tabs 4–5 "Language" / "Access" are added by addDefaultLanguageTab /
//           addDefaultAccessTab at the end of each CType definition.
// ============================================================================

$sharedTabs =
    "--div--;" .
    $lang("tab.appearance_layout") .
    "," .
    "--palette--;;maispace_design," .
    "--palette--;;maispace_spacing," .
    "--palette--;;maispace_responsive," .
    "--div--;" .
    $lang("tab.advanced") .
    "," .
    "--palette--;;maispace_animation," .
    "--palette--;;maispace_linking," .
    "--palette--;;maispace_advanced";

// ============================================================================
// CTYPE ITEM GROUPS
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
// BASIC ELEMENTS (12)
// text, heading, richtext, image, video, audio, button, linklist, icon,
// divider, spacer, embed
// ============================================================================

new CType("maispace_text", $lang("ctype.text"), "content-text")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
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
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_image", $lang("ctype.image"), "content-image")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addDefaultImageTab()
    ->addCustomFields("--palette--;;maispace_image_display")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_video", $lang("ctype.video"), "content-media")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addDefaultMediaTab()
    ->addCustomFields("--palette--;;maispace_video_providers")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_audio", $lang("ctype.audio"), "content-media")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addDefaultMediaTab()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_button", $lang("ctype.button"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields(
        "tx_maitheme_link, tx_maitheme_button_style, tx_maitheme_button_size",
    )
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_linklist", $lang("ctype.linklist"), "content-bullets")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_icon", $lang("ctype.icon"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_icon_identifier, tx_maitheme_icon_size")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_divider", $lang("ctype.divider"), "content-special-div")
    ->disableGeneralPalette()
    ->addCustomFields("tx_maitheme_divider_style")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_spacer", $lang("ctype.spacer"), "content-special-div")
    ->disableGeneralPalette()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_embed", $lang("ctype.embed"), "content-media")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
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
    ->addCustomFields(
        "tx_maitheme_eyebrow, bodytext, image," .
        "tx_maitheme_link, tx_maitheme_link_text," .
        "tx_maitheme_link_secondary, tx_maitheme_link_secondary_text," .
        "tx_maitheme_badge_text",
    )
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields(
        "--div--;" .
        $lang("tab.layout") .
        "," .
        "tx_maitheme_content_position, tx_maitheme_overlay_style," .
        "--palette--;;maispace_image_display," .
        "--palette--;;maispace_video_providers",
    )
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_page')
    ->register();

new CType("maispace_banner", $lang("ctype.banner"), "content-image")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields(
        "--div--;" .
        $lang("tab.layout") .
        "," .
        "tx_maitheme_content_position, tx_maitheme_overlay_style," .
        "--palette--;;maispace_image_display," .
        "--palette--;;maispace_video_providers",
    )
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_page')
    ->register();

new CType("maispace_cta", $lang("ctype.cta"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, tx_maitheme_link, tx_maitheme_link_text")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_page')
    ->register();

new CType("maispace_mediatext", $lang("ctype.mediatext"), "content-textmedia")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, tx_maitheme_media_position")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addDefaultImageTab()
    ->addCustomFields("--palette--;;maispace_image_display")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_page')
    ->register();

// ============================================================================
// CARDS & COMPONENTS (11)
// card, teaser, featurebox, profile, testimonial, quote, logo, logoshowcase,
// statistic, badge, contactinfo
// (pricebox, event, jobposting removed — belong in dedicated extensions)
// ============================================================================

new CType("maispace_card", $lang("ctype.card"), "content-textmedia")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields("--palette--;;maispace_image_display")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_teaser", $lang("ctype.teaser"), "content-textmedia")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image, tx_maitheme_link")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields("--palette--;;maispace_image_display")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_featurebox", $lang("ctype.featurebox"), "content-textmedia")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_icon_identifier, tx_maitheme_accent, bodytext, tx_maitheme_link, tx_maitheme_link_text")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_profile", $lang("ctype.profile"), "content-image")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields("--palette--;;maispace_image_display")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_testimonial", $lang("ctype.testimonial"), "content-quote")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, image")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
    ->addCustomFields("--palette--;;maispace_image_display")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_quote", $lang("ctype.quote"), "content-quote")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_logo", $lang("ctype.logo"), "content-image")
    ->addCustomFields("image, tx_maitheme_link")
    ->addCustomFields("--palette--;;maispace_image_display")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_logoshowcase", $lang("ctype.logoshowcase"), "content-image")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addDefaultImageTab()
    ->addCustomFields("--palette--;;maispace_image_display")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_statistic", $lang("ctype.statistic"), "content-table")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_value, tx_maitheme_unit")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_badge", $lang("ctype.badge"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_variant")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

new CType("maispace_contactinfo", $lang("ctype.contactinfo"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields(
        "tx_maitheme_address, tx_maitheme_phone, tx_maitheme_email, " .
        "tx_maitheme_opening_hours, tx_maitheme_link",
    )
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_components')
    ->register();

// ============================================================================
// UI WIDGETS (7)
// slider, accordion, tabs, modal, faq, beforeafter, steps
// (maispace_timeline is registered by EXT:mai_timeline)
// ============================================================================

new CType("maispace_slider", $lang("ctype.slider"), "content-carousel")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields(
        "tx_maitheme_slider_items",
    )
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_accordion", $lang("ctype.accordion"), "content-accordion")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_accordion_items")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_tabs", $lang("ctype.tabs"), "content-tab")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_tab_items")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_modal", $lang("ctype.modal"), "content-special-html")
     ->addDefaultHeaderPalette()
     ->addSubheaderField()
     ->addCustomFields("bodytext")
     ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
     ->addCustomFields($sharedTabs)
     ->addDefaultLanguageTab()
     ->addDefaultAccessTab()
     ->setGroup('maispace_widgets')
     ->register();

new CType("maispace_faq", $lang("ctype.faq"), "content-accordion")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_accordion_items")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_beforeafter", $lang("ctype.beforeafter"), "content-image")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_image_before, tx_maitheme_image_after")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

new CType("maispace_steps", $lang("ctype.steps"), "content-list-bullet")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_accordion_items")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_widgets')
    ->register();

// ============================================================================
// FORMS & ENGAGEMENT (3)
// form, newsletter, map
// (search removed — belongs in a search extension)
// ============================================================================

new CType("maispace_form", $lang("ctype.form"), "content-form")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_forms')
    ->register();

new CType("maispace_newsletter", $lang("ctype.newsletter"), "content-form")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_forms')
    ->register();

new CType("maispace_map", $lang("ctype.map"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext, tx_maitheme_lat, tx_maitheme_lng, tx_maitheme_zoom")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_forms')
    ->register();

// ============================================================================
// DATA & MEDIA (9)
// table, datalist, gallery, filelist, codeblock, chart, progressbar, rating,
// breadcrumb
// ============================================================================

new CType("maispace_table", $lang("ctype.table"), "content-table")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
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
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType("maispace_gallery", $lang("ctype.gallery"), "content-image-gallery")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addDefaultImageTab()
    ->addCustomFields("--palette--;;maispace_image_display")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType("maispace_filelist", $lang("ctype.filelist"), "content-filelinks")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addDefaultMediaTab()
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
    ->addDefaultHeaderPalette()
    ->addCustomFields("tx_maitheme_language, bodytext")
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
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
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
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_value, tx_maitheme_variant")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

new CType("maispace_rating", $lang("ctype.rating"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_rating_value, tx_maitheme_rating_max")
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_data')
    ->register();

// ============================================================================
// NAVIGATION & UTILITY (3)
// breadcrumb, socialmedia, alert
// (callout, iconlist, textcolumns put in basic group below)
// ============================================================================

new CType("maispace_breadcrumb", $lang("ctype.breadcrumb"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_socialmedia", $lang("ctype.socialmedia"), "content-bullets")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

// ============================================================================
// FEEDBACK (1 — editorial notices only)
// alert
// (notification, spinner, emptystate, confirmation removed — controller concerns)
// ============================================================================

new CType("maispace_alert", $lang("ctype.alert"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_variant, bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_feedback')
    ->register();

// ============================================================================
// ADDITIONAL BASIC ELEMENTS (missing from original list)
// callout, iconlist, textcolumns
// ============================================================================

new CType("maispace_callout", $lang("ctype.callout"), "content-special-html")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("tx_maitheme_variant, bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_iconlist", $lang("ctype.iconlist"), "content-bullets")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => false]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

new CType("maispace_textcolumns", $lang("ctype.textcolumns"), "content-text-mixed")
    ->addDefaultHeaderPalette()
    ->addSubheaderField()
    ->addCustomFields("bodytext")
    ->addColumnOverride("bodytext", ["config" => ["enableRichtext" => true]])
    ->addCustomFields($sharedTabs)
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_basic')
    ->register();

// ============================================================================
// INLINE CHILD-RECORD FIELD REGISTRATIONS ON tt_content
// These inline fields bind child record tables to their parent CTypes.
// ============================================================================

// Shared accordion/FAQ/steps items (tx_maitheme_accordion_item table)
$GLOBALS['TCA']['tt_content']['columns']['tx_maitheme_accordion_items'] = [
    'label' => $lang('field.items'),
    'config' => [
        'type' => 'inline',
        'foreign_table' => 'tx_maitheme_accordion_item',
        'foreign_field' => 'parent_uid',
        'foreign_sortby' => 'sort',
        'appearance' => [
            'collapseAll' => true,
            'expandSingle' => true,
            'newRecordLinkAddTitle' => true,
            'showSynchronizationLink' => true,
            'showAllLocalizationLink' => true,
            'showPossibleLocalizationRecords' => true,
        ],
    ],
];
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    'tx_maitheme_accordion_items',
    '',
    '',
);

// Tab items
$GLOBALS['TCA']['tt_content']['columns']['tx_maitheme_tab_items'] = [
    'label' => $lang('field.items'),
    'config' => [
        'type' => 'inline',
        'foreign_table' => 'tx_maitheme_tab_item',
        'foreign_field' => 'parent_uid',
        'foreign_sortby' => 'sort',
        'appearance' => [
            'collapseAll' => true,
            'expandSingle' => true,
            'newRecordLinkAddTitle' => true,
            'showSynchronizationLink' => true,
            'showAllLocalizationLink' => true,
            'showPossibleLocalizationRecords' => true,
        ],
    ],
];

// Slider items
$GLOBALS['TCA']['tt_content']['columns']['tx_maitheme_slider_items'] = [
    'label' => $lang('field.slides'),
    'config' => [
        'type' => 'inline',
        'foreign_table' => 'tx_maitheme_slider_item',
        'foreign_field' => 'parent_uid',
        'foreign_sortby' => 'sort',
        'appearance' => [
            'collapseAll' => true,
            'expandSingle' => true,
            'newRecordLinkAddTitle' => true,
            'showSynchronizationLink' => true,
            'showAllLocalizationLink' => true,
            'showPossibleLocalizationRecords' => true,
        ],
    ],
];

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
    foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as &$item) {
        if (isset($item['value']) && str_starts_with($item['value'], 'maispace_section_')) {
            $item['group'] = 'maispace_sections';
        }
    }
    unset($item);
}
