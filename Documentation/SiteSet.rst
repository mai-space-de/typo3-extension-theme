.. include:: /Documentation/Includes.rst.txt

.. _site-set:

Site Set — maispace/theme-base
==============================

``EXT:theme`` ships a reusable TYPO3 **site set** named ``maispace/theme-base``
that exposes all visual and structural options of the theme as first-class site
settings. Settings are editable in the TYPO3 backend under
:guilabel:`Site Management > Sites > <your site> > Settings` and require no
code changes or TypoScript knowledge.

.. contents:: On this page
   :local:
   :depth: 2

Overview
--------

Adding ``maispace/theme-base`` as a dependency of your site configuration
activates three automatic behaviours:

1. **Fluid template variables** — site settings are passed into all Fluid
   templates as ``{settings.*}`` (via ``page.10.settings``).
2. **CSS custom property overrides** — an inline ``<style>`` block is injected
   into ``<head>`` (``page.headerData.100``), re-declaring every design token
   CSS variable with the configured value. Because the SCSS bundle uses CSS
   custom properties throughout, colors and typography cascade into all
   components automatically without recompilation.
3. **Backend theming** — the ``BackendThemeFromSiteSettings`` PSR-15 middleware
   reads backend logo and login-page settings from the configured site and
   applies them globally to the TYPO3 backend.

Enabling the site set
---------------------

Open ``config/sites/<your-site>/config.yaml`` and add ``maispace/theme-base``
to the ``dependencies`` array:

.. code-block:: yaml

   # config/sites/my-site/config.yaml
   base: 'https://example.com/'
   rootPageId: 1
   sets:
     - maispace/theme-base

After saving the file (or flushing the TYPO3 cache), the site settings editor
will display all ``maispace Theme`` settings grouped by category.

.. tip::

   If you manage your sites through the TYPO3 backend site module, you can also
   add the dependency there: :guilabel:`Site Management > Sites > Edit site >
   Sets > Add "maispace Theme Base"`.

Available settings
------------------

All settings live under the ``maispace.theme.*`` namespace. Their identifiers
are also available as TypoScript constants with the same dotted path, e.g.
``{$maispace.theme.colors.primary}``.

Site Identity
~~~~~~~~~~~~~

.. list-table::
   :widths: 35 10 55
   :header-rows: 1

   * - Setting
     - Type
     - Description
   * - ``maispace.theme.site.name``
     - string
     - Site name used in browser tab titles (``<title>`` fallback) and the
       footer copyright notice. Default: ``TYPO3 Site``.
   * - ``maispace.theme.site.logoSrc``
     - string
     - Path to the frontend logo image shown in the site header. Supports
       ``EXT:`` paths and ``fileadmin/`` paths. When empty the site name is
       rendered as text. Default: empty.

Color Scheme
~~~~~~~~~~~~

All color settings accept any valid CSS color value.  The recommended format
is a 6-digit hex literal (``#rrggbb``) so the TYPO3 color picker can display
and edit it correctly.

Each color maps directly to a CSS custom property of the same name in the
SCSS bundle (see :ref:`stylesheets` for the full token list).

.. list-table::
   :widths: 35 10 55
   :header-rows: 1

   * - Setting
     - CSS custom property
     - Description
   * - ``maispace.theme.colors.primary``
     - ``--color-primary``
     - Main brand color for primary buttons, active links and focus rings.
       Default: ``#2563eb``.
   * - ``maispace.theme.colors.primaryHover``
     - ``--color-primary-hover``
     - Darker shade shown on hover for primary interactive elements.
       Default: ``#1d4ed8``.
   * - ``maispace.theme.colors.secondary``
     - ``--color-secondary``
     - Supporting brand color for secondary buttons and muted text.
       Default: ``#64748b``.
   * - ``maispace.theme.colors.secondaryHover``
     - ``--color-secondary-hover``
     - Darker shade shown on hover for secondary elements.
       Default: ``#475569``.
   * - ``maispace.theme.colors.accent``
     - ``--color-accent``
     - Highlight color for badges, tags and decorative elements.
       Default: ``#f59e0b``.
   * - ``maispace.theme.colors.text``
     - ``--color-text``
     - Default foreground color for body copy and headings.
       Default: ``#191919``.
   * - ``maispace.theme.colors.background``
     - ``--color-background``
     - Background color of the outermost page canvas (``<body>``).
       Default: ``#ffffff``.
   * - ``maispace.theme.colors.surface``
     - ``--color-surface``
     - Background color for raised surfaces such as cards and panels.
       Default: ``#f3f3f3``.
   * - ``maispace.theme.colors.border``
     - ``--color-border``
     - Default color for dividers, input borders and card outlines.
       Default: ``#d1d5db``.

Typography
~~~~~~~~~~

.. list-table::
   :widths: 35 10 55
   :header-rows: 1

   * - Setting
     - Type
     - Description
   * - ``maispace.theme.typography.fontFamilyBase``
     - string
     - CSS ``font-family`` stack for body text and UI elements.  Use a
       comma-separated list, e.g.
       ``'Inter', system-ui, sans-serif``.
       Self-hosted fonts must be loaded separately (e.g. via
       ``Configuration/StyleSheets.php``).
       Default: ``system-ui, -apple-system, sans-serif``.

Layout
~~~~~~

.. list-table::
   :widths: 35 10 55
   :header-rows: 1

   * - Setting
     - Type
     - Description
   * - ``maispace.theme.layout.rootPageUid``
     - int
     - UID of the site root page. Used as the ``href`` target for the header
       logo. Default: ``1``.
   * - ``maispace.theme.layout.borderRadius``
     - string
     - Default corner-rounding applied to cards, buttons and images. Any valid
       CSS length, e.g. ``0.5rem``, ``8px``, or ``0`` to disable rounding.
       Default: ``0.5rem``.
   * - ``maispace.theme.layout.maxWidth``
     - string
     - Maximum width of the page wrapper. Any valid CSS length, e.g.
       ``1400px`` or ``90rem``. Default: ``1400px``.
   * - ``maispace.theme.layout.sidebar``
     - bool
     - Enable the two-column sidebar layout (main content colPos 0 + sidebar
       colPos 1) on the default page template. Default: ``false``.
   * - ``maispace.theme.layout.showHeaderActions``
     - bool
     - Render the header ``actions`` named slot (language switcher, login
       button, etc.). Default: ``false``.
   * - ``maispace.theme.layout.showFooterNav``
     - bool
     - Render the footer ``nav`` named slot. Default: ``true``.
   * - ``maispace.theme.layout.footerColumns``
     - bool
     - Render the footer ``columns`` named slot inside an auto-grid. Default:
       ``false``.

Backend Theme
~~~~~~~~~~~~~

Backend theming is global — it applies to the entire TYPO3 backend, not to a
specific site. For single-site installations configure these settings on your
one site. For multi-site installations the **first site that has a non-empty
loginLogo** setting wins; alternatively, use :ref:`configuration-backend-theme`
for explicit global control.

.. list-table::
   :widths: 35 10 55
   :header-rows: 1

   * - Setting
     - Type
     - Description
   * - ``maispace.theme.backend.logo``
     - string
     - Logo in the TYPO3 backend toolbar (top-left corner). ``EXT:`` or
       ``fileadmin`` path. Recommended: SVG or PNG, max 500 × 120 px.
       Leave empty for the TYPO3 default.
   * - ``maispace.theme.backend.favicon``
     - string
     - Favicon shown in the browser tab when the backend is open. ``EXT:``
       or ``fileadmin`` path (.ico or .png).
       Leave empty for the TYPO3 default.
   * - ``maispace.theme.backend.loginLogo``
     - string
     - Logo above the login form. ``EXT:`` or ``fileadmin`` path.
       Recommended: SVG or PNG, max 300 px wide.
       Leave empty for the TYPO3 default.
   * - ``maispace.theme.backend.loginLogoAlt``
     - string
     - Alt text for the login logo (read by screen readers).
   * - ``maispace.theme.backend.loginBackground``
     - string
     - Full-bleed background image for the login page. ``EXT:`` or
       ``fileadmin`` path. Recommended: JPEG, min 1920 × 1080 px.
   * - ``maispace.theme.backend.loginHighlightColor``
     - color
     - Accent color for the primary button and focus rings on the login page.
       Default: ``#2563eb``.
   * - ``maispace.theme.backend.loginFootnote``
     - string
     - Short text below the login form (plain text, no HTML), e.g.
       ``© 2026 Acme Corp``.

How settings flow through the stack
-------------------------------------

The following diagram shows how a site setting value reaches the browser:

.. code-block:: text

   Site Settings editor (TYPO3 backend)
       ↓ stored in config/sites/<site>/settings.yaml
   TypoScript constants      {$maispace.theme.colors.primary} → #e11d48
       ↓ substituted at TS parse time
   page.headerData.100       <style>:root { --color-primary: #e11d48; }</style>
       ↓ rendered into <head>
   CSS cascade               --color-primary overrides the SCSS bundle default
       ↓ consumed by
   All components            buttons, links, focus rings use --color-primary

   AND simultaneously:

   TypoScript constants      {$maispace.theme.site.logoSrc} → EXT:my_site/...logo.svg
       ↓ substituted at TS parse time
   page.10.settings.site.logoSrc
       ↓ passed to Fluid as {settings.site.logoSrc}
   SiteHeader component      <img src="..." alt="{settings.site.name}" />

Using settings in custom TypoScript
-------------------------------------

Because site settings are exposed as TypoScript constants, they are available
anywhere in your setup TypoScript after the site set is active:

.. code-block:: typoscript

   # Read the configured primary color in your own TypoScript
   lib.myObject = TEXT
   lib.myObject.value = {$maispace.theme.colors.primary}

Using settings in custom Fluid templates
------------------------------------------

Settings that are mapped through ``page.10.settings`` (see the site set's
``setup.typoscript``) are available in all Fluid templates as ``{settings.*}``:

.. code-block:: html

   <!-- Access the configured site name -->
   <p>{settings.site.name}</p>

   <!-- Conditionally show sidebar -->
   <f:if condition="{settings.layout.sidebar}">
       <aside>...</aside>
   </f:if>

Overriding a CSS variable at higher specificity
------------------------------------------------

Site settings inject their CSS custom properties at ``headerData`` key 100.
Your site package can inject further overrides at a higher key (101+) to
layer additional changes on top:

.. code-block:: typoscript

   # Site package setup.typoscript — runs after the site set
   page.headerData.101 = TEXT
   page.headerData.101.value (<style>:root {
     --layout-radius: 0;       /* square corners for this project */
     --font-family-base: 'My Brand Font', system-ui, sans-serif;
   }</style>
   )

.. note::

   The CSS custom property overrides are injected as a plain ``<style>`` block
   and are therefore unaffected by the SCSS compilation cache. Changing a site
   setting takes effect immediately after clearing the page cache — no TYPO3
   cache flush or SCSS recompilation is required.

Dark mode
---------

The SCSS bundle contains built-in dark-mode overrides via
``@media (prefers-color-scheme: dark)``. The site settings color scheme applies
to the light-mode defaults. If you need to customise dark-mode tokens as well,
add them in a ``StyleSheets.php`` override file:

.. code-block:: css

   /* your_extension/Resources/Public/StyleSheet/dark-overrides.css */
   @media (prefers-color-scheme: dark) {
       :root {
           --color-primary: #60a5fa;  /* lighter blue for dark backgrounds */
       }
   }

See :ref:`configuration` for how to register a custom stylesheet via
``Configuration/StyleSheets.php``.
