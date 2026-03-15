.. include:: /Documentation/Includes.rst.txt

.. _stylesheets:

Stylesheets
===========

The stylesheet bundle follows the **ITCSS** (Inverted Triangle CSS) methodology
combined with **Atomic Design** — the same approach used in the
`minimal-stylesheet-maximum-impact`_ reference project that the bundle is
derived from.

.. _minimal-stylesheet-maximum-impact: https://github.com/mai-space/minimal-stylesheet-maximum-impact

How compilation works
---------------------

The bundle entry point is ``EXT:theme/Resources/Private/StyleSheets/bundle.scss``.
Compilation, caching, and registration are entirely handled by `maispace/assets`_
using ``scssphp`` — no Node.js or build pipeline required, and no custom PHP
code in the theme extension is involved in the SCSS pipeline.

The ``<mai:scss>`` ViewHelper is placed in the base Fluid layout:

.. code-block:: html

   <!-- EXT:theme/Resources/Private/Layouts/Page/Default.html -->
   <mai:scss
       src="EXT:theme/Resources/Private/StyleSheets/bundle.scss"
       identifier="maispace-theme-bundle"
       priority="1"
       minify="1"
   />

``maispace/assets`` caches the compiled CSS in ``typo3temp/`` and invalidates
the cache automatically when the source ``.scss`` file changes.

Default import paths
~~~~~~~~~~~~~~~~~~~~~

The theme TypoScript registers the SCSS directory as a ``defaultImportPaths``
entry so downstream SCSS files can import partials with short paths:

.. code-block:: typoscript

   plugin.tx_maispace_assets.scss.defaultImportPaths = EXT:theme/Resources/Private/StyleSheets

This means any ``<mai:scss>``-compiled file in any extension can write:

.. code-block:: scss

   @use "02-mixins/media-breakpoint" as *;   // ✓ short path
   @use "01-settings/variables";             // ✓ short path

.. _maispace/assets: https://github.com/mai-space-de/typo3-extension-assets

CSS Layers
----------

The bundle declares an explicit ``@layer`` order at the top of
``bundle.scss``. This guarantees predictable specificity regardless of import
order:

.. code-block:: scss

   @layer theme-settings,
          theme-generic,
          theme-atoms,
          theme-molecules,
          theme-organisms,
          theme-templates,
          theme-utilities;

Each ITCSS partial wraps its output in the matching layer (e.g.
``@layer theme-atoms { … }``). Downstream stylesheets that declare their own
layers after the theme layers will always win in specificity, making overrides
straightforward.

The theme TypoScript also configures a dedicated ``theme-critical`` layer for
inlined critical CSS extracted by ``maispace/assets``:

.. code-block:: typoscript

   plugin.tx_maispace_assets.criticalCss.layer = theme-critical

Design tokens (CSS custom properties)
--------------------------------------

Every design decision is expressed as a CSS custom property defined in
``01-settings/_variables.scss``. Properties are grouped by concern:

Colours
~~~~~~~

.. list-table::
   :widths: 35 20 45
   :header-rows: 1

   * - Property
     - Default
     - Purpose
   * - ``--color-text``
     - ``#191919``
     - Default body text
   * - ``--color-surface``
     - ``#f3f3f3``
     - Card / panel background
   * - ``--color-background``
     - ``#ffffff``
     - Page background
   * - ``--color-primary``
     - ``#2563eb``
     - Main brand / interaction colour
   * - ``--color-primary-hover``
     - ``#1d4ed8``
     - Darkened primary for hover states
   * - ``--color-secondary``
     - ``#64748b``
     - Muted secondary colour
   * - ``--color-secondary-hover``
     - ``#475569``
     - Darkened secondary for hover states
   * - ``--color-accent``
     - ``#f59e0b``
     - Highlight / decoration colour
   * - ``--color-success``
     - ``#16a34a``
     - Success / valid state
   * - ``--color-warning``
     - ``#d97706``
     - Warning state
   * - ``--color-danger``
     - ``#dc2626``
     - Error / invalid state
   * - ``--color-info``
     - ``#0891b2``
     - Informational state
   * - ``--color-border``
     - ``#d1d5db``
     - Default border colour
   * - ``--color-shadow``
     - ``rgb(0 0 0 / 10%)``
     - Box-shadow colour
   * - ``--color-disabled-bg``
     - ``#e5e7eb``
     - Disabled element background
   * - ``--color-disabled-text``
     - ``#9ca3af``
     - Disabled element text
   * - ``--color-link``
     - ``var(--color-primary)``
     - Default link colour
   * - ``--color-link-hover``
     - ``var(--color-secondary)``
     - Link hover colour
   * - ``--color-link-visited``
     - ``var(--color-secondary)``
     - Visited link colour

Spacing
~~~~~~~

.. list-table::
   :widths: 25 20 55
   :header-rows: 1

   * - Property
     - Default
     - Purpose
   * - ``--space-2xs``
     - ``0.125rem``
     - Icon padding, tiny gaps
   * - ``--space-xs``
     - ``0.25rem``
     - Tight gaps
   * - ``--space-sm``
     - ``0.5rem``
     - Button padding, list gaps
   * - ``--space-md``
     - ``1rem``
     - Default component padding
   * - ``--space-lg``
     - ``1.5rem``
     - Section margins
   * - ``--space-xl``
     - ``2rem``
     - Large vertical rhythm
   * - ``--space-2xl``
     - ``3rem``
     - Section spacing
   * - ``--space-3xl``
     - ``4rem``
     - Hero / section padding
   * - ``--space-em-xs``
     - ``0.25em``
     - Relative tight gap (inside components)
   * - ``--space-em-sm``
     - ``0.5em``
     - Relative small gap
   * - ``--space-em-md``
     - ``1em``
     - Relative default gap
   * - ``--space-em-lg``
     - ``1.5em``
     - Relative large gap

Layout
~~~~~~

.. list-table::
   :widths: 30 20 50
   :header-rows: 1

   * - Property
     - Default
     - Purpose
   * - ``--layout-width-min``
     - ``320px``
     - Minimum page width
   * - ``--layout-width-max``
     - ``1400px``
     - Maximum page width
   * - ``--layout-content-width``
     - ``75ch``
     - Default content column width
   * - ``--layout-card-width``
     - ``32ch``
     - Ideal card width for auto-grid
   * - ``--layout-text-width``
     - ``65ch``
     - Optimal line length for readability
   * - ``--layout-sidebar-main``
     - ``75ch``
     - Main column in sidebar layout
   * - ``--layout-sidebar-aside``
     - ``45ch``
     - Sidebar column width
   * - ``--layout-grid-gap``
     - ``clamp(…, 3vw, …)``
     - Responsive grid gap
   * - ``--layout-grid-min-col``
     - ``4rem``
     - Minimum column width in auto-grid
   * - ``--layout-radius``
     - ``0.5rem``
     - Default border radius
   * - ``--layout-radius-lg``
     - ``1rem``
     - Large border radius
   * - ``--layout-radius-full``
     - ``9999px``
     - Pill / fully rounded

Typography
~~~~~~~~~~

.. list-table::
   :widths: 30 30 40
   :header-rows: 1

   * - Property
     - Default
     - Purpose
   * - ``--font-family-base``
     - ``system-ui, -apple-system, sans-serif``
     - Body font
   * - ``--font-family-accent``
     - ``var(--font-family-base)``
     - Heading font (override for a display typeface)
   * - ``--font-family-mono``
     - ``ui-monospace, "Cascadia Code", monospace``
     - Monospace / code font
   * - ``--font-weight-normal``
     - ``400``
     - Normal text weight
   * - ``--font-weight-medium``
     - ``500``
     - Medium weight (buttons, labels)
   * - ``--font-weight-bold``
     - ``700``
     - Bold text weight
   * - ``--font-weight-black``
     - ``800``
     - Extra-bold / black weight
   * - ``--font-size-xs``
     - ``clamp(0.64rem, …, 0.72rem)``
     - Smallest fluid text size
   * - ``--font-size-sm``
     - ``clamp(0.8rem, …, 0.9rem)``
     - Small text
   * - ``--font-size-base``
     - ``clamp(1rem, …, 1.125rem)``
     - Default fluid body size
   * - ``--font-size-lg``
     - ``clamp(1.25rem, …, 1.4rem)``
     - Large text / h4
   * - ``--font-size-xl``
     - ``clamp(1.563rem, …, 1.76rem)``
     - h3 size
   * - ``--font-size-2xl``
     - ``clamp(1.953rem, …, 2.2rem)``
     - h2 size
   * - ``--font-size-3xl``
     - ``clamp(2.441rem, …, 2.75rem)``
     - Large heading size
   * - ``--font-size-4xl``
     - ``clamp(3.052rem, …, 3.44rem)``
     - h1 size
   * - ``--line-height-tight``
     - ``1.1``
     - Headings
   * - ``--line-height-snug``
     - ``1.3``
     - Compact text
   * - ``--line-height-base``
     - ``1.5``
     - Default body line height
   * - ``--line-height-relaxed``
     - ``1.75``
     - Spacious text

Buttons
~~~~~~~

.. list-table::
   :widths: 35 25 40
   :header-rows: 1

   * - Property
     - Default
     - Purpose
   * - ``--btn-padding-y``
     - ``var(--space-sm)``
     - Vertical padding
   * - ``--btn-padding-x``
     - ``var(--space-lg)``
     - Horizontal padding
   * - ``--btn-font-size``
     - ``var(--font-size-base)``
     - Button font size
   * - ``--btn-font-weight``
     - ``var(--font-weight-medium)``
     - Button font weight
   * - ``--btn-border-radius``
     - ``var(--layout-radius)``
     - Button border radius
   * - ``--btn-border-width``
     - ``2px``
     - Button border width
   * - ``--btn-primary-bg``
     - ``var(--color-primary)``
     - Primary button background
   * - ``--btn-primary-text``
     - ``#ffffff``
     - Primary button text colour
   * - ``--btn-secondary-bg``
     - ``var(--color-secondary)``
     - Secondary button background
   * - ``--btn-secondary-text``
     - ``#ffffff``
     - Secondary button text colour
   * - ``--btn-outline-bg``
     - ``transparent``
     - Outline button background
   * - ``--btn-outline-text``
     - ``var(--color-primary)``
     - Outline button text colour
   * - ``--btn-sm-padding-y``
     - ``var(--space-xs)``
     - Small button vertical padding
   * - ``--btn-sm-font-size``
     - ``var(--font-size-sm)``
     - Small button font size
   * - ``--btn-lg-padding-y``
     - ``var(--space-md)``
     - Large button vertical padding
   * - ``--btn-lg-font-size``
     - ``var(--font-size-lg)``
     - Large button font size

Images
~~~~~~

.. list-table::
   :widths: 30 30 40
   :header-rows: 1

   * - Property
     - Default
     - Purpose
   * - ``--img-border-radius``
     - ``var(--layout-radius)``
     - Default image border radius
   * - ``--img-shadow``
     - ``0 4px 6px …``
     - Default image box-shadow
   * - ``--img-object-fit``
     - ``cover``
     - Default object-fit for images

Icons
~~~~~

.. list-table::
   :widths: 25 20 55
   :header-rows: 1

   * - Property
     - Default
     - Purpose
   * - ``--icon-size-sm``
     - ``1rem``
     - Small icon size
   * - ``--icon-size-md``
     - ``1.25rem``
     - Medium icon size (default)
   * - ``--icon-size-lg``
     - ``1.5rem``
     - Large icon size
   * - ``--icon-color``
     - ``currentcolor``
     - Default icon fill colour

Override any property in ``:root`` to change the whole bundle:

.. code-block:: css

   :root {
       --color-primary:     #7c3aed;   /* violet-600 */
       --font-family-accent: 'Playfair Display', serif;
       --layout-radius:     0rem;      /* sharp corners */
   }

Dark mode
---------

The bundle includes automatic dark-mode support via
``@media (prefers-color-scheme: dark)`` inside ``01-settings/_variables.scss``.
When the user's operating system or browser is set to dark mode the following
tokens are overridden:

.. list-table::
   :widths: 35 25 40
   :header-rows: 1

   * - Property
     - Dark value
     - Notes
   * - ``--color-text``
     - ``#f3f3f3``
     - Light text on dark background
   * - ``--color-surface``
     - ``#1e293b``
     - Dark card / panel background
   * - ``--color-background``
     - ``#0f172a``
     - Dark page background
   * - ``--color-border``
     - ``#334155``
     - Subtle dark-mode borders
   * - ``--color-shadow``
     - ``rgb(0 0 0 / 30%)``
     - Stronger shadow in dark mode
   * - ``--btn-primary-bg-hover``
     - ``color-mix(…, 80%, white)``
     - Lighter primary hover
   * - ``--btn-outline-bg-hover``
     - ``color-mix(…, 80%, transparent)``
     - Translucent outline hover

No extra imports or configuration are required — dark mode works out of the box.
To disable it, override the affected properties inside your own
``:root`` declarations.

Breakpoint mixins
-----------------

The ``02-mixins/_media-breakpoint.scss`` partial provides three SCSS mixins.
Import the partial with ``@use`` to use them in your own SCSS files:

.. code-block:: scss

   @use 'EXT:theme/Resources/Private/StyleSheets/02-mixins/media-breakpoint' as *;

   .my-component {
       display: block;

       @include bp-up(md) {
           display: flex;
       }

       @include bp-down(sm) {
           padding: var(--space-sm);
       }
   }

Available breakpoints:

.. list-table::
   :widths: 20 20 60
   :header-rows: 1

   * - Name
     - Value
     - Notes
   * - ``xs``
     - ``0``
     - All viewports (no media query emitted)
   * - ``sm``
     - ``36rem``
     - Phones landscape / small tablets
   * - ``md``
     - ``48rem``
     - Tablets
   * - ``lg``
     - ``62rem``
     - Laptops
   * - ``xl``
     - ``75rem``
     - Desktops
   * - ``2xl``
     - ``87.5rem``
     - Wide screens

Component overview
------------------

Atoms
~~~~~

**Button** (``04-atoms/_button.scss``)
  Applies to ``<button>``, ``<a class="button">``, and ``<input type="submit">``.
  Variants: ``.button-secondary``, ``.button-outline``.
  Sizes: ``.button-sm``, ``.button-lg``.
  Block: ``.button-block``.

**Link** (``04-atoms/_link.scss``)
  Styled with ``--color-link``. Accessible ``:focus-visible`` ring.
  ``.link-unstyled`` removes all decoration (useful for card wrappers).

**Image** (``04-atoms/_image.scss``)
  ``.img-fluid``, ``.img-cover``, ``.img-contain``.
  ``.img-rounded``, ``.img-circle``, ``.img-shadow``.

**List** (``04-atoms/_list.scss``)
  ``.list-unstyled``, ``.list-inline``.
  Custom markers: ``.list-check``, ``.list-arrow``, ``.list-bullet``.

**Table** (``04-atoms/_table.scss``)
  ``.table-striped``, ``.table-bordered``, ``.table-sm``.
  ``.table-responsive`` for mobile label-per-row layout (requires
  ``data-label`` attributes on ``<td>``).

**Accessibility** (``04-atoms/_accessibility.scss``)
  ``.visually-hidden``, ``.skip-link``, global ``:focus-visible`` ring,
  ``prefers-reduced-motion`` and ``prefers-contrast`` overrides.

Molecules
~~~~~~~~~

**Card** (``05-molecules/_card.scss``)
  ``.card`` wrapper with ``.card-image``, ``.card-body``, ``.card-title``,
  ``.card-meta``, ``.card-text``, ``.card-footer``.
  ``.card-clickable`` adds lift-and-shadow hover state.

**External link** (``05-molecules/_external-link.scss``)
  Automatically injects contextual icons via ``::before``/``::after``:
  ↗ for ``https://``, ✉ for ``mailto:``, ☎ for ``tel:``, ⬇ for download links.
  Suppress with ``.internal-link``.

**Form field** (``05-molecules/_form-field.scss``)
  ``.form-field`` stacks label + input. Error state via ``.form-field-error``
  on the wrapper. Helper text via ``.form-field-help``.
  Error message via ``.form-field-error-message``.

**Button group** (``05-molecules/_button-group.scss``)
  ``.button-group`` — loose flex row.
  ``.button-group-attached`` — fused borders, shared outer radius only.

**Media object** (``05-molecules/_media-object.scss``)
  ``.media-object`` with ``.media-object-figure`` and ``.media-object-body``.
  Stacks below ``sm`` breakpoint or via ``.media-object-stacked``.

Organisms
~~~~~~~~~

**Header** (``06-organisms/_header.scss``)
  ``.site-header`` — sticky header, ``--header-height`` custom property.
  ``.site-header-logo``, ``.site-header-actions`` slots.

**Navigation** (``06-organisms/_navigation.scss``)
  ``.navigation`` base. Variants: ``.navigation-horizontal``,
  ``.navigation-vertical``. Dropdown via ``.navigation-dropdown`` on ``<li>``.
  Breadcrumb via ``.breadcrumb``. Mobile toggle pattern via
  ``.navigation-mobile-toggle`` + ``aria-expanded``.

**Form** (``06-organisms/_form.scss``)
  ``.form`` stacked layout. Grid: ``.form-grid``. Row: ``.form-row``.
  Inline: ``.form-inline``. Search: ``.form-search``. Actions: ``.form-actions``.
  Validation: ``.form-success``, ``.form-error``.

Utilities
~~~~~~~~~

See ``08-utilities/_classes.scss`` for the full list. Highlights:

* Text: ``.text-center``, ``.text-start``, ``.text-end``
* Colour: ``.color-primary``, ``.color-secondary``, etc.
* Display: ``.d-none``, ``.d-flex``, ``.d-grid``, etc.
* Layout: ``.w-full``, ``.mx-auto``, ``.container``, ``.flow``
* Text overflow: ``.truncate``, ``.line-clamp`` (``--clamp-lines: 3``)
* Full-bleed breakout: ``.full-bleed`` (``--full-bleed-bg``)
