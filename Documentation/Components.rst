.. include:: /Documentation/Includes.rst.txt

.. _components:

Components
==========

``EXT:theme`` ships six pre-built `sitegeist/fluid-components`_ components.
They cover the most common UI building blocks and are designed to be used
directly or overridden in a site package.

.. _sitegeist/fluid-components: https://github.com/sitegeist/fluid-components

Namespace registration
----------------------

Register the component namespace once per template file (or in the shared
layout):

.. code-block:: html

   {namespace theme=Maispace\Theme\Components}

After that, all components are available as ``<theme:atom.*>``,
``<theme:molecule.*>``, and ``<theme:organism.*>`` tags.

File structure
--------------

.. code-block:: text

   Resources/Private/Components/
   ├── Atom/
   │   ├── Button/Button.html
   │   └── Link/Link.html
   ├── Molecule/
   │   └── Card/Card.html
   └── Organism/
       ├── Navigation/Navigation.html
       ├── SiteHeader/SiteHeader.html
       └── SiteFooter/SiteFooter.html

Overriding a component
-----------------------

Register an additional ``componentPrefixPaths`` entry in your site package's
TypoScript so your own version takes precedence:

.. code-block:: typoscript

   plugin.tx_fluidcomponents.settings.componentPrefixPaths {
       10 = EXT:your_site/Resources/Private/Components/
   }

Any component that exists at the same relative path in your extension will
shadow the base component. For example, to replace only the Button atom:

.. code-block:: text

   your_site/Resources/Private/Components/Atom/Button/Button.html

Atoms
-----

Atom/Button
~~~~~~~~~~~

Renders a ``<button>`` element styled with the ``.btn`` utility class from
``04-atoms/_button.scss``.

**Parameters**

.. list-table::
   :widths: 15 10 15 60
   :header-rows: 1

   * - Name
     - Type
     - Default
     - Description
   * - ``label``
     - string
     - ``""``
     - Visible button text. Overridden by slot content when provided.
   * - ``type``
     - string
     - ``"button"``
     - HTML ``type`` attribute: ``button``, ``submit``, or ``reset``.
   * - ``variant``
     - string
     - ``"primary"``
     - CSS modifier appended as ``btn--{variant}``. Use any variant defined in
       ``04-atoms/_button.scss`` (e.g. ``secondary``, ``ghost``).
   * - ``disabled``
     - boolean
     - ``false``
     - Adds the ``disabled`` attribute and ``aria-disabled="true"`` when set.

**Examples**

.. code-block:: html

   {namespace theme=Maispace\Theme\Components}

   {{-- Simple submit button --}}
   <theme:atom.button label="Save" type="submit" variant="primary" />

   {{-- Secondary button with slot content --}}
   <theme:atom.button variant="secondary">Cancel</theme:atom.button>

   {{-- Disabled button --}}
   <theme:atom.button label="Unavailable" disabled="1" />

Atom/Link
~~~~~~~~~

A reusable anchor atom. The ``href`` parameter is resolved via
``f:uri.typolink``, so all TYPO3 link types work correctly.

**Parameters**

.. list-table::
   :widths: 15 10 15 60
   :header-rows: 1

   * - Name
     - Type
     - Default
     - Description
   * - ``href``
     - string
     - *(required)*
     - Typolink target: page UID, ``t3://page?uid=…``, or a full URL.
   * - ``label``
     - string
     - ``""``
     - Link text. Overridden by slot content when provided.
   * - ``target``
     - string
     - ``""``
     - HTML ``target`` attribute (e.g. ``_blank``).
   * - ``external``
     - boolean
     - ``false``
     - Adds ``rel="noopener noreferrer"`` automatically when set.

**Examples**

.. code-block:: html

   {namespace theme=Maispace\Theme\Components}

   {{-- External link with rel --}}
   <theme:atom.link href="https://typo3.org" label="TYPO3" external="1" />

   {{-- Internal page link via t3:// --}}
   <theme:atom.link href="t3://page?uid=1">Home</theme:atom.link>

   {{-- Link by page UID --}}
   <theme:atom.link href="42" label="Contact" />

Molecules
---------

Molecule/Card
~~~~~~~~~~~~~

A presentational card molecule combining an optional media area, a title
(optionally linked), and a content slot. Styled via
``05-molecules/_card.scss``.

**Parameters**

.. list-table::
   :widths: 15 10 15 60
   :header-rows: 1

   * - Name
     - Type
     - Default
     - Description
   * - ``title``
     - string
     - *(required)*
     - Card heading text.
   * - ``link``
     - string
     - ``""``
     - Optional URL. When set, the title is rendered as a linked heading.
   * - ``image``
     - string
     - ``""``
     - Optional image path or identifier for the card media area.
   * - ``imageAlt``
     - string
     - ``""``
     - Alt text for the card image.

**Slot**

The default slot renders inside the card body as descriptive text.

**Example**

.. code-block:: html

   {namespace theme=Maispace\Theme\Components}

   <theme:molecule.card
       title="TYPO3"
       link="https://typo3.org"
       image="EXT:my_site/Resources/Public/Images/typo3.jpg"
       imageAlt="TYPO3 logo"
   >
       The professional, flexible Content Management System.
   </theme:molecule.card>

Organisms
---------

Organism/Navigation
~~~~~~~~~~~~~~~~~~~

Renders a two-level navigation list from a structured pages array. Active pages
receive ``aria-current="page"`` automatically.

**Parameters**

.. list-table::
   :widths: 15 10 15 60
   :header-rows: 1

   * - Name
     - Type
     - Default
     - Description
   * - ``pages``
     - array
     - *(required)*
     - Array of navigation items. Each item must have ``uid`` (integer),
       ``title`` (string), and ``isActive`` (boolean). An optional ``children``
       key holds a nested array with the same structure.

**Example**

.. code-block:: html

   {namespace theme=Maispace\Theme\Components}

   <nav class="navigation navigation-horizontal" aria-label="Main navigation">
       <theme:organism.navigation pages="{settings.navigationPages}" />
   </nav>

.. note::

   This component renders only the ``<ul>`` list. The wrapping ``<nav>``
   element and its ``aria-label`` should be provided by the parent context
   (as done in ``Organism/SiteHeader``).

Organism/SiteHeader
~~~~~~~~~~~~~~~~~~~

Full site header organism: logo link, main navigation, and an optional named
slot for actions (search, CTA, language switcher). Styled via
``06-organisms/_header.scss``.

**Parameters**

.. list-table::
   :widths: 20 10 15 55
   :header-rows: 1

   * - Name
     - Type
     - Default
     - Description
   * - ``siteName``
     - string
     - *(required)*
     - Site name used as the logo ``aria-label`` and fallback text when no
       logo image is set.
   * - ``rootPageUid``
     - integer
     - *(required)*
     - UID of the root page; used as the logo link target.
   * - ``navigationPages``
     - array
     - ``{}``
     - Array of navigation items (same structure as Organism/Navigation).
   * - ``logoSrc``
     - string
     - ``""``
     - Optional ``EXT:`` path to the logo image. Falls back to site name text.
   * - ``showActions``
     - boolean
     - ``false``
     - Renders the ``actions`` named slot when true.

**Named slot**

``actions`` — Content rendered inside ``.site-header-actions``. Pass it with
``<fc:pass slot="actions">…</fc:pass>``.

**Example**

.. code-block:: html

   {namespace theme=Maispace\Theme\Components}

   <theme:organism.siteHeader
       siteName="{settings.site.name}"
       rootPageUid="{settings.rootPageUid}"
       navigationPages="{settings.navigationPages}"
       logoSrc="{settings.site.logoSrc}"
       showActions="1"
   >
       <fc:pass slot="actions">
           <a href="/search" class="btn btn--ghost">Search</a>
       </fc:pass>
   </theme:organism.siteHeader>

Organism/SiteFooter
~~~~~~~~~~~~~~~~~~~

Full site footer organism: optional columns area, copyright line, and an
optional footer navigation.

**Parameters**

.. list-table::
   :widths: 20 10 15 55
   :header-rows: 1

   * - Name
     - Type
     - Default
     - Description
   * - ``siteName``
     - string
     - *(required)*
     - Site name displayed in the copyright notice.
   * - ``showNav``
     - boolean
     - ``true``
     - Renders the ``nav`` named slot inside a ``<nav>`` element when true.
   * - ``showColumns``
     - boolean
     - ``false``
     - Renders the ``columns`` named slot inside an ``.auto-grid`` container
       when true.

**Named slots**

* ``columns`` — Content rendered inside the ``.auto-grid`` columns area.
* ``nav`` — Navigation markup rendered inside the footer ``<nav>``.

**Example**

.. code-block:: html

   {namespace theme=Maispace\Theme\Components}

   <theme:organism.siteFooter
       siteName="{settings.site.name}"
       showNav="1"
       showColumns="1"
   >
       <fc:pass slot="columns">
           <div><h3>About</h3><p>…</p></div>
           <div><h3>Contact</h3><p>…</p></div>
       </fc:pass>
       <fc:pass slot="nav">
           <f:for each="{footerPages}" as="page">
               <a href="{f:uri.page(pageUid: page.uid)}">{page.title}</a>
           </f:for>
       </fc:pass>
   </theme:organism.siteFooter>
