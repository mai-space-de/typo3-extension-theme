# maispace/theme — TYPO3 Theme Loader

[![CI](https://github.com/mai-space-de/typo3-extension-theme/actions/workflows/ci.yml/badge.svg)](https://github.com/mai-space-de/typo3-extension-theme/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net/)
[![TYPO3](https://img.shields.io/badge/TYPO3-13.0%2B-orange)](https://typo3.org/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)

This extension provides three things:

1. **An ITCSS stylesheet bundle** — compiled server-side via [`maispace/assets`](https://github.com/mai-space-de/typo3-extension-assets) using `<mai:scss>`, no Node.js required.
2. **A loader mechanism** — auto-discovers `StyleSheets.php`, `JavaScripts.php`, and `BackendTheme.php` from all active TYPO3 packages and registers their assets automatically.
3. **Fluid Components** — a set of reusable `sitegeist/fluid-components` components (Button, Link, Card, Navigation, SiteHeader, SiteFooter) ready to use or override in any site package.

## Features

- **ITCSS stylesheet bundle** — 22 SCSS partials across 8 layers (settings → utilities), derived from [minimal-stylesheet-maximum-impact](https://github.com/mai-space/minimal-stylesheet-maximum-impact)
- **Full CSS Layers support** — the bundle and all partials are wrapped in `@layer` blocks for predictable specificity
- **CSS custom properties throughout** — every design token is overridable without touching source files
- **Atomic Design structure** — atoms, molecules, organisms, templates, and utilities
- **Fluid Components** — six pre-built components (Atom/Button, Atom/Link, Molecule/Card, Organism/Navigation, Organism/SiteHeader, Organism/SiteFooter) backed by `sitegeist/fluid-components`
- **Base Fluid page templates** — layout, templates, and partials ready to override in your site package
- **Server-side SCSS compilation** — delegated to `maispace/assets` (`<mai:scss>` ViewHelper, powered by `scssphp`)
- **Automated asset inclusion** — auto-registers stylesheets and JavaScripts from any active extension
- **Backend theme management** — logos, favicon, and login-page customisation via configuration files
- **Configuration merging** — merges configuration files from all active packages for modular theme development

## Installation

```bash
composer require maispace/theme
```

Import TypoScript in your site package's setup file:

```typoscript
@import 'EXT:maispace_assets/Configuration/TypoScript/setup.typoscript'
@import 'EXT:theme/Configuration/TypoScript/setup.typoscript'
```

### Critical CSS & Layers

The theme is pre-configured to work with `maispace/assets`'s critical CSS extraction. It defines a dedicated CSS layer `theme-critical` in its TypoScript setup:

```typoscript
plugin.tx_maispace_assets.criticalCss.layer = theme-critical
```

This ensures that any inlined critical CSS (extracted via `maispace:assets:critical:extract`) is wrapped in `@layer theme-critical { ... }`, providing predictable specificity when used alongside the theme's main SCSS bundle.

## Stylesheet customisation

Override any CSS custom property in `:root` to customise the design system:

```css
/* your_extension/Resources/Public/StyleSheet/overrides.css */
:root {
    --color-primary:      #7c3aed;
    --font-family-accent: 'Playfair Display', serif;
    --layout-radius:      0rem;
}
```

Register overrides via `Configuration/StyleSheets.php` in your extension — they are picked up automatically.

## Usage

Create configuration files in your extension's `Configuration/` directory:

### StyleSheets.php
```php
<?php
return [
    'frontend' => [
        'my_stylesheet' => [
            'source' => 'EXT:my_extension/Resources/Public/Css/style.css',
            'site-identifier' => 'my_site' // Optional: filter by site identifier
        ],
    ],
];
```

### JavaScripts.php
```php
<?php
return [
    'frontend' => [
        'my_script' => [
            'source' => 'EXT:my_extension/Resources/Public/Js/script.js',
            'site-identifier' => 'my_site'
        ],
    ],
];
```

### BackendTheme.php
```php
<?php
return [
    'backendLogo' => 'EXT:my_extension/Resources/Public/Icons/logo.svg',
    'loginBackgroundImage' => 'EXT:my_extension/Resources/Public/Images/login-bg.jpg',
    'loginHighlightColor' => '#2563eb',
];
```

The `theme` extension will automatically find these files in any active package and apply the configurations.

## Fluid Components

The extension ships six pre-built [`sitegeist/fluid-components`](https://github.com/sitegeist/fluid-components) components. Register the namespace in your templates:

```html
{namespace theme=Maispace\Theme\Components}
```

### Atom/Button

```html
<theme:atom.button label="Save" type="submit" variant="primary" />
<theme:atom.button variant="secondary">Cancel</theme:atom.button>
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `label` | `""` | Visible button text (used when no slot content is provided) |
| `type` | `"button"` | HTML button type: `button`, `submit`, or `reset` |
| `variant` | `"primary"` | CSS modifier class appended as `btn--{variant}` |
| `disabled` | `false` | Disables the button and sets `aria-disabled` |

### Atom/Link

```html
<theme:atom.link href="https://typo3.org" label="TYPO3" external="1" />
<theme:atom.link href="t3://page?uid=1">Home</theme:atom.link>
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `href` | *(required)* | Typolink target (page UID, `t3://`, or full URL) |
| `label` | `""` | Link text; overridden by slot content when provided |
| `target` | `""` | HTML `target` attribute (e.g. `_blank`) |
| `external` | `false` | Adds `rel="noopener noreferrer"` automatically |

### Molecule/Card

```html
<theme:molecule.card title="TYPO3" link="https://typo3.org" image="..." imageAlt="TYPO3 Logo">
    The professional, flexible Content Management System.
</theme:molecule.card>
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `title` | *(required)* | Card heading text |
| `link` | `""` | Optional URL; when set the title becomes a linked heading |
| `image` | `""` | Optional image path or identifier for the card media area |
| `imageAlt` | `""` | Alt text for the image |

### Organism/Navigation

```html
<theme:organism.navigation pages="{settings.navigationPages}" />
```

| Parameter | Description |
|-----------|-------------|
| `pages` | Array of navigation items with `uid`, `title`, `isActive`, and optional `children` |

### Organism/SiteHeader

```html
<theme:organism.siteHeader
    siteName="{settings.site.name}"
    rootPageUid="{settings.rootPageUid}"
    navigationPages="{settings.navigationPages}"
    logoSrc="{settings.site.logoSrc}"
    showActions="{settings.header.showActions}"
/>
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `siteName` | *(required)* | Site name used as logo aria-label and fallback text |
| `rootPageUid` | *(required)* | Root page UID for the logo link |
| `navigationPages` | `{}` | Array of navigation items (see Organism/Navigation) |
| `logoSrc` | `""` | Optional image path; falls back to site name text |
| `showActions` | `false` | Renders the `actions` named slot when true |

### Organism/SiteFooter

```html
<theme:organism.siteFooter
    siteName="{settings.site.name}"
    showNav="{settings.footer.showNav}"
    showColumns="{settings.footer.columns}"
/>
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `siteName` | *(required)* | Site name shown in the copyright notice |
| `showNav` | `true` | Renders the `nav` named slot when true |
| `showColumns` | `false` | Renders the `columns` named slot inside an `.auto-grid` |

## SCSS bundle structure

```
Resources/Private/StyleSheets/
├── bundle.scss                    ← ITCSS entry point (compiled by <mai:scss>)
├── 01-settings/
│   └── _variables.scss            ← All CSS custom properties
├── 02-mixins/
│   └── _media-breakpoint.scss     ← bp-up(), bp-down(), bp-only()
├── 03-generic/
│   ├── _reset.scss
│   ├── _layout.scss               ← Grid, sidebar, reel, container…
│   └── _typography.scss
├── 04-atoms/
│   ├── _accessibility.scss
│   ├── _button.scss
│   ├── _image.scss
│   ├── _link.scss
│   ├── _list.scss
│   └── _table.scss
├── 05-molecules/
│   ├── _button-group.scss
│   ├── _card.scss
│   ├── _external-link.scss
│   ├── _form-field.scss
│   └── _media-object.scss
├── 06-organisms/
│   ├── _form.scss
│   ├── _header.scss
│   └── _navigation.scss
├── 07-templates/
│   └── _shame.scss
└── 08-utilities/
    └── _classes.scss
```

## Template structure

```
Resources/Private/
├── Components/
│   ├── Atom/
│   │   ├── Button/Button.html     ← fc:component — button atom
│   │   └── Link/Link.html         ← fc:component — anchor atom
│   ├── Molecule/
│   │   └── Card/Card.html         ← fc:component — card molecule
│   └── Organism/
│       ├── Navigation/Navigation.html   ← fc:component — two-level nav list
│       ├── SiteHeader/SiteHeader.html   ← fc:component — full site header
│       └── SiteFooter/SiteFooter.html   ← fc:component — full site footer
├── Layouts/Page/Default.html      ← <mai:scss> injection point
├── Templates/Page/Default.html    ← Page template (sidebar-aware)
└── Partials/Page/
    ├── Header.html
    ├── Footer.html
    └── Navigation.html
```

Override any template by registering a higher-priority `templateRootPaths` / `partialRootPaths` key in your site package's TypoScript.

## Development

-   **Backend Theme**: Call `GeneralUtility::makeInstance(BackendTheme::class)->registerBackendTheme()` in your `ext_localconf.php` to apply backend settings.

## License

GPL-2.0-or-later
