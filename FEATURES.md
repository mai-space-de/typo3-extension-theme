# `mai_theme` Feature Reference

> Canonical reference for `maispace/mai-theme` (TYPO3 14.x).
> Authoritative source for the Site Set, design tokens, content-element catalogue,
> backend theme, and extension API.

---

## 1. Site Set

**Set identifier:** `maispace/mai-theme`
**Declared in:** `Configuration/Sets/MaiTheme/config.yaml`

```yaml
name: maispace/mai-theme
label: Maispace Default Theme
```

The Site Set is activated per-site in the TYPO3 Sites module ("Site Configuration →
Dependencies"). Once active, all settings exposed by `settings.definitions.yaml` become
available in the Sites module UI and are injected into TypoScript as `{$key}` constants.

---

## 2. Site Settings — Migration from `constants.typoscript`

### Background

Before TYPO3 14.x, extension Site Sets could ship a `constants.typoscript` file to
provide TypoScript constant defaults. This extension previously had:

```
# Configuration/Sets/MaiTheme/constants.typoscript (deleted May 2026)
pageUids.mainMenu = 1
```

**This file was deleted** when the Site Sets `settings.definitions.yaml` mechanism was
adopted. In TYPO3 14.x the `constants.typoscript` approach is superseded and removed.

### New approach — `settings.definitions.yaml`

All theme-configurable values are now declared in:

```
Configuration/Sets/MaiTheme/settings.definitions.yaml
```

Each entry has a **type**, a **default** value, and a **category** for UI grouping.
TYPO3 stores the site-specific overrides in `config/sites/<site>/settings.yaml`.
TypoScript setup files read the resolved value via `{$key.name}` syntax — the same
`{$…}` token that previously resolved TypoScript constants.

### Migration guide for integrators

| Old (removed) | New |
|---|---|
| `constants.typoscript` record in page tree | TYPO3 Sites module → Site Settings tab |
| `pageUids.mainMenu = 123` in a TypoScript constants record | Set `pageUids.mainMenu: 123` in the Sites module |
| `{$pageUids.mainMenu}` reference in setup | unchanged — still `{$pageUids.mainMenu}` |

**There are no TypoScript constants records to maintain.** Editors configure all
theme values once in the Sites module. The `{$…}` tokens in TypoScript setup files
resolve identically to before.

### Settings reference

Settings are grouped into four categories (see `settings.definitions.yaml` for the full
list with defaults):

#### `general.*` — Site identity

| Key | Type | Default | Purpose |
|---|---|---|---|
| `general.site.logo` | string | `''` | Path to the site logo (FAL reference or `EXT:` path) |
| `general.site.logoAlt` | string | `''` | Alt text for the site logo |
| `general.site.favicon` | string | `''` | Path to the site favicon |

#### `frontend.*` — Design tokens

Over 60 typed settings defining CSS custom-property values (see § 3 for how they are
emitted). Sub-namespaces:

| Sub-namespace | Coverage |
|---|---|
| `frontend.color.*` | 15 semantic colour tokens (text, surface, background, primary, secondary, accent, feedback, border, disabled) |
| `frontend.dark.*` | 4 dark-mode colour overrides |
| `frontend.space.*` | 8 spacing scale steps (2xs → 3xl) |
| `frontend.layout.*` | 10 layout dimensions (widthMin/Max, contentWidth, cardWidth, textWidth, sidebar, gridGap, radius) |
| `frontend.font.*` | 18 typography tokens (family, weight, size, line-height) |
| `frontend.btn.*` | 6 button tokens (bg, text, radius, border-width for primary/secondary) |
| `frontend.img.*` | 2 image tokens (border-radius, object-fit) |
| `frontend.icon.*` | 3 icon size tokens (sm/md/lg) |

#### `pageUids.*` — Structural page references

| Key | Default | Purpose |
|---|---|---|
| `pageUids.mainMenu` | `1` | Root page for the main navigation |
| `pageUids.footerMenu` | `1` | Root page for the footer navigation |
| `pageUids.homepage` | `0` | Home page UID (used in breadcrumbs, logo links) |
| `pageUids.imprint` | `0` | Imprint / legal notice page |
| `pageUids.dataProtectionPolicy` | `0` | Privacy policy page |
| `pageUids.accessibilityStatement` | `0` | Accessibility statement page |
| `pageUids.contact` | `0` | Contact page |
| `pageUids.search` | `0` | Search results page |
| `pageUids.disclaimer` | `0` | Disclaimer page |
| `pageUids.newsletter` | `0` | Newsletter sign-up page |
| `pageUids.frontendLogin` | `0` | Frontend login page |

#### `elements.*` — Content element visibility toggles

Boolean settings that activate or suppress individual CType registrations at
`ext_localconf.php` load time. Groups:

| Setting prefix | Covers |
|---|---|
| `elements.core.disableAll` | Suppress all TYPO3 built-in CTypes (default: `true`) |
| `elements.basic.*` | text, heading, richtext, image, video, audio, button, linklist, icon, divider, breadcrumb, socialmedia, embed, spacer, callout, iconlist, textcolumns |
| `elements.page.*` | hero, banner, cta, mediatext |
| `elements.components.*` | card, teaser, featurebox, profile, testimonial, quote, logo, logoshowcase, statistic, badge, contactinfo |
| `elements.widgets.*` | slider, accordion, tabs, modal, timeline, faq, beforeafter, steps |
| `elements.forms.*` | form, newsletter, map |
| `elements.data.*` | table, datalist, gallery, filelist, codeblock, chart, progressbar, rating |
| `elements.feedback.*` | alert |
| `elements.sections.*` | full, fiftyFifty, twoThirdOneThird, oneThirdTwoThird, threeColumn, fourColumn, sidebarRight, sidebarLeft |

---

## 3. Design Token Pipeline

Settings under `frontend.*` are emitted as CSS custom properties via `lib.themeVars`.

**TypoScript object:** `lib.themeVars` (defined in
`Configuration/TypoScript/Setup/lib/themeVars.typoscript`)

**Output:** A `:root { --mai-* }` block included in the page `<head>` as a critical
inline style via `<mai:asset.criticalStyle identifier="mai-theme-vars" isCritical="1">`.

**Naming convention (deterministic 1:1 with YAML keys):**

| YAML key | CSS custom property |
|---|---|
| `frontend.color.primary` | `--mai-color-primary` |
| `frontend.color.primaryHover` | `--mai-color-primary-hover` |
| `frontend.layout.widthMax` | `--mai-layout-width-max` |
| `frontend.font.lineHeightTight` | `--mai-font-line-height-tight` |
| `frontend.space.2xl` | `--mai-space-2xl` |
| `frontend.btn.primaryBg` | `--mai-btn-primary-bg` |

The rule: `frontend.` prefix stripped, `.` → `-`, camelCase → kebab-case.

**Total tokens emitted:** ~60 CSS custom properties (see `themeVars.typoscript` for the
exact list).

---

## 4. Page Template

**Entry point:** `Configuration/Sets/MaiTheme/setup.typoscript`

```
@import 'EXT:mai_theme/Configuration/TypoScript/Setup/lib.typoscript'
@import 'EXT:mai_theme/Configuration/TypoScript/Setup/page.typoscript'
```

### `lib.typoscript`

Imports five `lib.*` definitions:

| Object | File | Purpose |
|---|---|---|
| `lib.templates.columns.*` | `lib/columns.typoscript` | `styles.content.get` shortcuts for 7 colPos slots (content=0, popovers=1, navbar=2, beforeContent=3, afterContent=4, footer=5, hidden=6) |
| `lib.contentRecord.*` | `lib/contentRecord.typoscript` | Shared rendering base for non-page-tree records |
| `lib.dynamicContent` | `lib/dynamicContent.typoscript` | Dynamic content injection utility |
| `lib.contentElement` | `lib/contentElement.typoscript` | Shared FLUIDTEMPLATE base for all 50+ `maispace_*` CTypes (§ 5) |
| `lib.themeVars` | `lib/themeVars.typoscript` | CSS custom-property emitter (§ 3) |

### `page.typoscript`

Sets up the TYPO3 `PAGE` object:

- Fluid page template: `EXT:mai_theme/Resources/Private/Templates/Page/Default.html`
- Template / partial / layout root paths at priority 100 (override-friendly)
- Two `MenuProcessor` data processors (`menuMain` from `{$pageUids.mainMenu}`,
  `footerMain` from `{$pageUids.footerMenu}`)
- Passes all `pageUids.*`, `general.*`, and `frontend.*` settings forward into
  `settings` so Fluid templates can read them via `{settings.pids.imprint}` etc.
- Enables `mai_assets` HTML minification:
  `plugin.tx_maispace_assets.settings.htmlMinification.enable = 1`
- Imports `page/body.typoscript` for the `<body>` tag builder
  (`page-{uid}` id attribute)

### Fluid namespace

```typoscript
config.namespaces.theme = Maispace\MaiTheme\Components
plugin.tx_fluidcomponents.namespaces.Maispace\MaiTheme\Components
    = EXT:mai_theme/Resources/Private/Components
```

The `<theme:…>` namespace resolves to `mai_theme`'s Fluid components directory.

---

## 5. Content Element Catalogue

All custom CTypes follow a single rendering base: `lib.contentElement`.

### `lib.contentElement` base

- **TypoScript type:** `FLUIDTEMPLATE`
- **Template root:** `EXT:mai_theme/Resources/Private/Templates/ContentElements/`
  at priority 100 (downstream extensions may override at a higher priority)
- **Shared FAL processors** (run on every CType, yield empty arrays when the field
  does not exist on that CType):

| Slot | `references.fieldName` | Fluid variable |
|---|---|---|
| 10 | `image` | `{images}` |
| 11 | `assets` | `{assets}` |
| 12 | `tx_maitheme_bg_image` | `{bgImage}` |
| 13 | `tx_maitheme_video_poster` | `{videoPoster}` |
| 14 | `tx_maitheme_image_before` | `{imageBefore}` |
| 15 | `tx_maitheme_image_after` | `{imageAfter}` |

Shared page UID settings forwarded to all content elements via
`lib.contentElement.settings.pids.*`.

### Content element groups and CTypes

**Basic (17 CTypes)**

`maispace_text`, `maispace_heading`, `maispace_richtext`, `maispace_image`,
`maispace_video`, `maispace_audio`, `maispace_button`, `maispace_linklist`,
`maispace_icon`, `maispace_divider`, `maispace_spacer`, `maispace_embed`,
`maispace_breadcrumb`, `maispace_socialmedia`, `maispace_callout`,
`maispace_iconlist`, `maispace_textcolumns`

**Page (4 CTypes)**

`maispace_hero`, `maispace_banner`, `maispace_cta`, `maispace_mediatext`

**Components (11 CTypes)**

`maispace_card`, `maispace_teaser`, `maispace_featurebox`, `maispace_profile`,
`maispace_testimonial`, `maispace_quote`, `maispace_logo`, `maispace_logoshowcase`,
`maispace_statistic`, `maispace_badge`, `maispace_contactinfo`

* Content icons — Featurebox / Icon CEs use a curated Phosphor-fill SVG select
  (`tx_maitheme_icon_identifier`); backend shows icons via Icon API; frontend
  renders through `<mai:svg.icon>` sprite (`ContentIconCatalog`)

**Widgets (8 CTypes + aliases)**

`maispace_textmedia` (dedicated processor: `image` field, variable `image`);
`maispace_accordion` (loads `tx_maitheme_accordion_item` children ordered by `sort`);
`maispace_faq` / `maispace_steps` alias `maispace_accordion` processor;
`maispace_slider` (loads `tx_maitheme_slider_item` with nested `FilesProcessor`);
`maispace_tabs` (loads `tx_maitheme_tab_item`);
`maispace_tab` aliases `maispace_tabs`;
`maispace_timeline` (loads `tx_maitheme_timeline_item` with nested `FilesProcessor`);
`maispace_modal`, `maispace_beforeafter`

**Forms (3 CTypes)**

`maispace_form`, `maispace_newsletter`, `maispace_map`

**Data (8 CTypes)**

`maispace_table`, `maispace_datalist`, `maispace_gallery`, `maispace_filelist`,
`maispace_codeblock`, `maispace_chart`, `maispace_progressbar`, `maispace_rating`

**Feedback (1 CType)**

`maispace_alert`

### Section containers (b13/container, 8 CTypes)

Each uses `B13\Container\DataProcessing\ContainerProcessor` to expose child columns.
The template hardcodes the expected colPos iteration.

| CType | Template | Layout |
|---|---|---|
| `maispace_section_full` | `SectionFull` | Single full-width column |
| `maispace_section_50_50` | `Section5050` | Two 50 % columns |
| `maispace_section_66_33` | `Section6633` | Two-thirds / one-third |
| `maispace_section_33_66` | `Section3366` | One-third / two-thirds |
| `maispace_section_3col` | `Section3Col` | Three equal columns |
| `maispace_section_4col` | `Section4Col` | Four equal columns |
| `maispace_section_sidebar_r` | `SectionSidebarR` | Content + right sidebar |
| `maispace_section_sidebar_l` | `SectionSidebarL` | Left sidebar + content |

### Bento grid (b13/container, 1 CType)

`maispace_bento` — single `ContainerProcessor` at colPos 200; template `BentoBox`.
Child elements set their own column/row span via `b13/container` configuration.

---

## 6. Backend Theme

**Service class:** `Maispace\MaiTheme\Service\BackendThemeService`

`BackendThemeService` reads `Configuration/BackendTheme.php` arrays from all loaded
extensions (via `ActiveExtensionConfigurationLoader::getMergedConfigurationByFilename('BackendTheme')`)
and writes recognised keys into `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']`.

**Supported settings keys:**

```php
'backendFavicon'       => string  // path or URL
'backendLogo'          => string  // path or URL
'loginBackgroundImage' => string  // path or URL
'loginFootnote'        => string  // HTML string
'loginHighlightColor'  => string  // hex colour
'loginLogo'            => string  // path or URL
'loginLogoAlt'         => string  // alt text for login logo
```

**Providing a backend theme from another extension:**

```php
// packages/typo3-extension-<name>/Configuration/BackendTheme.php
return [
    'loginLogo'    => 'EXT:my_ext/Resources/Public/Images/logo.svg',
    'loginLogoAlt' => 'My Organisation',
    'loginHighlightColor' => '#2563EB',
];
```

**Entry point:** `BackendThemeService::applyTheme()` (static; called from
`ext_localconf.php` before DI is available) and `BackendThemeService::apply()` (DI
instance method, fires `AfterBackendThemeAppliedEvent`).

---

## 7. Fluid Components Layer

**Package:** `sitegeist/fluid-components`
**Namespace declared:** `Maispace\MaiTheme\Components`
**Namespace alias:** `theme` (accessible as `<theme:ComponentName … />`)
**Component root:** `EXT:mai_theme/Resources/Private/Components/`

This namespace is declared **only in `mai_theme`**. Extensions that render frontend
output must declare `mai_theme` as a dependency; they do not re-declare the namespace.

---

## 8. EXT:form Finisher

**Class:** `Maispace\MaiTheme\Finisher\ThemeMailFinisher`

A stub finisher that collects form data and is intended to delegate to
`mai_mail` for actual dispatch. The implementation body is intentionally empty —
mail sending must never be added directly here (architecture constraint: only
`mai_mail` dispatches email).

**Default options:**

| Option | Type | Purpose |
|---|---|---|
| `subject` | string | Email subject line |
| `recipient` | string | Recipient email address |
| `template` | string | Mail template identifier |

---

## 9. PSR-14 Events

### `BeforeComponentRenderedEvent`

**Namespace:** `Maispace\MaiTheme\Event`
**Dispatched:** before a `sitegeist/fluid-components` component renders.

| Method | Description |
|---|---|
| `getComponentIdentifier(): string` | The component identifier (e.g. `hero`) |
| `getArguments(): array` | Current component arguments array |
| `setArguments(array): void` | Replace arguments (passed to the component on render) |
| `cancel(): void` | Suppress rendering entirely |
| `isCancelled(): bool` | Whether rendering has been cancelled |

### `AfterBackendThemeAppliedEvent`

**Namespace:** `Maispace\MaiTheme\Event`
**Dispatched:** after `BackendThemeService::apply()` writes to `TYPO3_CONF_VARS`.

| Method | Description |
|---|---|
| `getAppliedSettings(): array` | Settings keys actually written to TYPO3_CONF_VARS |
| `setAppliedSettings(array): void` | Modify the reported set (does not re-apply) |

---

## 10. Architecture Constraints

- **SCSS** — `mai_theme` is the sole owner of frontend SCSS entry points. SCSS compilation
  itself lives in `mai_assets` (`scssphp/scssphp`); never add `scssphp/scssphp` here.
- **Fluid components** — `sitegeist/fluid-components` is only declared in `mai_theme`.
  All feature extensions render via `<theme:…>` components; they must not define
  standalone markup.
- **Mail** — `ThemeMailFinisher` must delegate to `mai_mail`. Never add
  `symfony/mailer` or inline mail dispatch to this extension.
- **Site Settings** — Do not add a new `constants.typoscript` file. New configurable
  values belong in `settings.definitions.yaml` with a `type`, `default`, and `category`.
  The `{$…}` token syntax in TypoScript setup resolves them automatically.
- **Content element toggles** — All `elements.*` boolean settings must be checked in
  `ext_localconf.php` before calling `ExtensionUtility::registerPlugin()` so that
  disabled elements are silently skipped without triggering TYPO3 warnings.
- **Layer rule** — `mai_theme` is in the Theme & Mail layer. It may depend on
  `mai_base` (Infrastructure) and `mai_assets` (Theme & Mail peer). Feature extensions
  depend on `mai_theme`, not the reverse.
