# maispace/theme — TYPO3 Theme Loader

[![CI](https://github.com/mai-space-de/typo3-extension-theme/actions/workflows/ci.yml/badge.svg)](https://github.com/mai-space-de/typo3-extension-theme/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://www.php.net/)
[![TYPO3](https://img.shields.io/badge/TYPO3-13.0%2B-orange)](https://typo3.org/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)

This extension provides two things:

1. **An ITCSS stylesheet bundle** — compiled server-side via [`maispace/assets`](https://github.com/mai-space-de/typo3-extension-assets) using `<mai:scss>`, no Node.js required.
2. **A loader mechanism** — auto-discovers `StyleSheets.php`, `JavaScripts.php`, and `BackendTheme.php` from all active TYPO3 packages and registers their assets automatically.

## Features

- **ITCSS stylesheet bundle** — 22 SCSS partials across 8 layers (settings → utilities), derived from [minimal-stylesheet-maximum-impact](https://github.com/mai-space/minimal-stylesheet-maximum-impact)
- **Full CSS Layers support** — the bundle and all partials are wrapped in `@layer` blocks for predictable specificity
- **CSS custom properties throughout** — every design token is overridable without touching source files
- **Atomic Design structure** — atoms, molecules, organisms, templates, and utilities
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
