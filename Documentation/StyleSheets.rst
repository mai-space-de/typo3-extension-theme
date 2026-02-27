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
It is compiled to CSS by `maispace/assets`_ using ``scssphp`` — entirely in PHP,
with no Node.js or build pipeline required.

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

.. _maispace/assets: https://github.com/mai-space-de/typo3-extension-assets

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
   * - ``--color-primary``
     - ``#2563eb``
     - Main brand / interaction colour
   * - ``--color-primary-hover``
     - ``#1d4ed8``
     - Darkened primary for hover states
   * - ``--color-secondary``
     - ``#64748b``
     - Muted secondary colour
   * - ``--color-accent``
     - ``#f59e0b``
     - Highlight / decoration colour
   * - ``--color-text``
     - ``#191919``
     - Default body text
   * - ``--color-background``
     - ``#ffffff``
     - Page background
   * - ``--color-surface``
     - ``#f3f3f3``
     - Card / panel background
   * - ``--color-border``
     - ``#d1d5db``
     - Default border colour
   * - ``--color-success``
     - ``#16a34a``
     - Success / valid state
   * - ``--color-warning``
     - ``#d97706``
     - Warning state
   * - ``--color-danger``
     - ``#dc2626``
     - Error / invalid state

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
   * - ``--font-size-base``
     - ``clamp(1rem, …, 1.125rem)``
     - Default fluid body size
   * - ``--font-size-4xl``
     - ``clamp(3.052rem, …, 3.44rem)``
     - h1 size

Override any property in ``:root`` to change the whole bundle:

.. code-block:: css

   :root {
       --color-primary:     #7c3aed;   /* violet-600 */
       --font-family-accent: 'Playfair Display', serif;
       --layout-radius:     0rem;      /* sharp corners */
   }

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
