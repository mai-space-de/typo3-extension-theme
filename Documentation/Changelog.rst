.. include:: /Documentation/Includes.rst.txt

.. _changelog:

Changelog
=========

13.0.0 — 2026-02-27
---------------------

Added
~~~~~

* **ITCSS stylesheet bundle** — 22 SCSS partials across 8 layers ported and
  extended from `minimal-stylesheet-maximum-impact`_:

  * ``01-settings/_variables.scss`` — complete design token system as CSS
    custom properties (colours, spacing, typography, component tokens,
    dark-mode overrides)
  * ``02-mixins/_media-breakpoint.scss`` — ``bp-up()``, ``bp-down()``,
    ``bp-only()`` responsive mixins
  * ``03-generic/_reset.scss`` — minimal box-model reset with colour-scheme
    declaration
  * ``03-generic/_layout.scss`` — main grid, sidebar, reel, flex-grid,
    auto-grid, card-grid, cluster, container, section, and flow utilities
  * ``03-generic/_typography.scss`` — fluid type scale, heading hierarchy,
    inline elements, blockquotes, and code blocks
  * ``04-atoms/_accessibility.scss`` — visually-hidden, skip link,
    focus-visible ring, reduced-motion and contrast overrides
  * ``04-atoms/_button.scss`` — primary, secondary, and outline button
    variants with sm/lg size modifiers and block layout
  * ``04-atoms/_link.scss`` — accessible link styles with ``link-unstyled``
    utility
  * ``04-atoms/_image.scss`` — fluid, cover, contain, rounded, circle, and
    shadow image helpers; figure / figcaption
  * ``04-atoms/_list.scss`` — base list styles, unstyled, inline, check,
    arrow, and bullet variants
  * ``04-atoms/_table.scss`` — striped, bordered, compact, and responsive
    (mobile label-per-row) table styles
  * ``05-molecules/_card.scss`` — card component with image, body, title,
    meta, text, and footer slots; clickable hover variant
  * ``05-molecules/_external-link.scss`` — automatic contextual icons for
    external, mailto, tel, and download links
  * ``05-molecules/_form-field.scss`` — label + input molecule with focus,
    disabled, error, and help-text states
  * ``05-molecules/_button-group.scss`` — loose and attached button group
    variants
  * ``05-molecules/_media-object.scss`` — figure + body side-by-side layout
    with responsive stacking
  * ``06-organisms/_header.scss`` — sticky site header with logo and actions
    slots
  * ``06-organisms/_navigation.scss`` — horizontal, vertical, dropdown,
    breadcrumb, and mobile toggle navigation patterns
  * ``06-organisms/_form.scss`` — stacked, grid, row, inline, and search form
    layouts with actions row and validation states
  * ``07-templates/_shame.scss`` — documented shame file for temporary hacks
  * ``08-utilities/_classes.scss`` — text, colour, display, flexbox, sizing,
    overflow, truncation, and full-bleed utilities

* **``bundle.scss``** — ITCSS entry point; compiled by ``<mai:scss>`` from
  ``maispace/assets`` — no custom PHP compilation code in the theme extension

* **Base Fluid page templates**:

  * ``Layouts/Page/Default.html`` — full HTML document with ``<mai:scss>``
    compilation of the stylesheet bundle
  * ``Templates/Page/Default.html`` — default page template with optional
    sidebar layout
  * ``Partials/Page/Header.html`` — site header partial with logo, navigation,
    and actions slots
  * ``Partials/Page/Footer.html`` — site footer with column and nav slots
  * ``Partials/Page/Navigation.html`` — main navigation partial with
    ``aria-current="page"`` support

* **``Configuration/TypoScript/setup.typoscript``** — registers the ``page``
  object, ``lib.content``, and ``lib.sidebar`` with overridable template paths
  and ``settings`` TypoScript; sets
  ``plugin.tx_maispace_assets.scss.defaultImportPaths`` so downstream SCSS
  can import theme partials with short paths

* **``composer.json``** — added ``maispace/assets ^13.0`` as a dependency;
  SCSS compilation is delegated to ``<mai:scss>`` instead of being handled
  directly in PHP

* **RST documentation** — Introduction, Installation, StyleSheets, Templates,
  and Changelog pages

.. _minimal-stylesheet-maximum-impact: https://github.com/mai-space/minimal-stylesheet-maximum-impact
