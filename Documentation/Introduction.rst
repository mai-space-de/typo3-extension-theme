.. include:: /Documentation/Includes.rst.txt

.. _introduction:

Introduction
============

**maispace/theme** handles two complementary concerns in a TYPO3 13 project:

* **Asset loading** — auto-discovers ``StyleSheets.php`` and ``JavaScripts.php``
  configuration files from every active TYPO3 package and registers the listed
  assets with TYPO3's *AssetCollector*.

* **Backend theming** — reads ``BackendTheme.php`` from active packages and
  applies logo, favicon, and login-page overrides to the TYPO3 backend.

In addition, this extension ships a complete front-end stylesheet system built
on the `minimal-stylesheet-maximum-impact`_ methodology and a set of base
Fluid page templates. Both are ready to use out of the box and straightforward
to override in a site package.

.. _minimal-stylesheet-maximum-impact: https://github.com/mai-space/minimal-stylesheet-maximum-impact

Features
--------

* **ITCSS stylesheet bundle** — 22 SCSS partials organised in 8 layers
  (settings → utilities). Compiled server-side by `maispace/assets`_ using
  ``scssphp``; no Node.js build pipeline required.

* **CSS custom properties throughout** — every design token (colour, spacing,
  typography, component sizing) is exposed as a ``--variable`` that can be
  overridden in ``:root`` or any scoping selector.

* **Atomic Design structure** — layers map to *settings*, *generic*, *atoms*,
  *molecules*, *organisms*, *templates*, and *utilities*, making it trivial to
  locate and extend individual components.

* **Base Fluid page templates** — a default layout (``Page/Default.html``),
  page template, header, navigation, and footer partials. Override any partial
  by registering a higher-priority ``partialRootPaths`` entry in TypoScript.

* **Automated asset merging** — ``FrontendAssetConfigurationsListener`` scans
  every active extension for ``Configuration/StyleSheets.php`` and
  ``Configuration/JavaScripts.php`` and registers their assets automatically.

* **Backend theme customisation** — logos, favicon, login background, and
  highlight colour are all configurable through ``Configuration/BackendTheme.php``
  in any active extension.

.. _maispace/assets: https://github.com/mai-space-de/typo3-extension-assets

Stylesheet layers at a glance
------------------------------

.. list-table::
   :widths: 15 15 70
   :header-rows: 1

   * - Folder
     - Layer
     - Contents
   * - ``01-settings``
     - Settings
     - CSS custom properties — colours, spacing, typography, component tokens,
       dark-mode overrides
   * - ``02-mixins``
     - Mixins
     - ``bp-up()``, ``bp-down()``, ``bp-only()`` responsive breakpoint mixins
       (no CSS output)
   * - ``03-generic``
     - Generic
     - Box-sizing reset, base HTML element styles, layout primitives (grid,
       sidebar, reel, container…)
   * - ``04-atoms``
     - Atoms
     - Button, link, image, list, table, accessibility utilities
   * - ``05-molecules``
     - Molecules
     - Card, form-field, external-link icons, button-group, media-object
   * - ``06-organisms``
     - Organisms
     - Site header, navigation (horizontal/vertical/dropdown/breadcrumb),
       complete form organism
   * - ``07-templates``
     - Templates
     - Shame file — temporary workarounds with mandatory comments
   * - ``08-utilities``
     - Utilities
     - Single-purpose helper classes (text alignment, colours, spacing, display…)
