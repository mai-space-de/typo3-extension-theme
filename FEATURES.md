## Component Layer

* Fluid Components — all frontend UI components defined as `sitegeist/fluid-components`
* Asset pipeline — integrates `mai_assets` for SCSS compilation, minification, and SVG sprites
* Design tokens — SCSS variables and CSS custom properties for colors, spacing, typography
* Page templates — base Fluid page templates for standard layouts
* Responsive images — image rendering configurations with srcset support
* Backend theme — custom backend logo and color scheme via `BackendThemeService`

## Custom Content Elements

* Custom CTypes — additional content element definitions registered via TCA
* Fluid templates — each element has a Fluid template using theme components
* TypoScript rendering — elements registered and rendered via TypoScript
* Rich text editing — CKEditor 5 configuration and presets via `cms-rte-ckeditor`
* Form rendering — TYPO3 form framework (EXT:form) integration and custom finishers
* Fluid styled content — base rendering for TYPO3 core content elements via `cms-fluid-styled-content`

## Section Containers

* Section containers — backend content elements acting as layout wrappers
* Visual variants — light, dark, accent, and transparent section backgrounds
* Inner column support — columns within sections via `b13/container`
* Content type restriction — permitted CTypes per column via `ichhabrecht/content-defender`
* Anchor navigation — slug-based in-page anchor IDs on content elements via `sebkln/content-slug`

## Bento-Box Grid

* Bento container element — a backend content element acting as a grid container
* Configurable grid — column and row span settings per child element via `b13/container`
* Content type restriction — only permitted CTypes may be placed inside cells via `ichhabrecht/content-defender`
* Visual variants — card, highlight, and media layout styles
