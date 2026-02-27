.. include:: /Documentation/Includes.rst.txt

.. _templates:

Templates
=========

``EXT:theme`` ships a minimal but complete set of base Fluid page templates.
They are designed to be extended or overridden by site packages — not to be
used as-is in production without customisation.

File structure
--------------

.. code-block:: text

   Resources/Private/
   ├── Layouts/
   │   └── Page/
   │       └── Default.html        ← <mai:scss> lives here
   ├── Partials/
   │   └── Page/
   │       ├── Header.html
   │       ├── Footer.html
   │       └── Navigation.html
   └── Templates/
       └── Page/
           └── Default.html

How the SCSS is injected
-------------------------

``<mai:scss>`` from :t3ext:`maispace_assets` is called inside the base layout:

.. code-block:: html

   <!-- EXT:theme/Resources/Private/Layouts/Page/Default.html -->
   <mai:scss
       src="EXT:theme/Resources/Private/StyleSheets/bundle.scss"
       identifier="maispace-theme-bundle"
       priority="1"
       minify="1"
   />

This single tag compiles the entire ITCSS bundle, minifies the output, and
places the ``<link rel="stylesheet">`` in the ``<head>`` section — with the
compiled file cached in ``typo3temp/`` and automatically invalidated on change.

Overriding templates
--------------------

Register additional paths at a higher numeric key in your site package's
TypoScript. Keys are sorted ascending; the first match wins.

.. code-block:: typoscript

   page.10 {
       templateRootPaths {
           0 = EXT:theme/Resources/Private/Templates/       # base (this ext)
           10 = EXT:your_site/Resources/Private/Templates/  # your override
       }

       partialRootPaths {
           0 = EXT:theme/Resources/Private/Partials/
           10 = EXT:your_site/Resources/Private/Partials/
       }

       layoutRootPaths {
           0 = EXT:theme/Resources/Private/Layouts/
           10 = EXT:your_site/Resources/Private/Layouts/
       }
   }

Any file that exists in your extension at the same relative path will
shadow the base template. For example, to replace only the navigation
partial, create:

.. code-block:: text

   your_site/Resources/Private/Partials/Page/Navigation.html

Adding your own SCSS alongside the bundle
-----------------------------------------

Place an additional ``<mai:scss>`` call in the overridden layout **after** the
base bundle, or register a plain CSS file via ``Configuration/StyleSheets.php``
in your site package:

.. code-block:: html

   <!-- your_site/Resources/Private/Layouts/Page/Default.html -->
   <f:layout name="Page/Default" />

   {{-- Extend the base layout to inject a second SCSS file --}}
   <mai:scss
       src="EXT:your_site/Resources/Private/StyleSheets/site.scss"
       identifier="your-site-scss"
       priority="1"
   />

Alternatively, keep the layout unchanged and register a CSS file via the
theme extension's auto-discovery mechanism:

.. code-block:: php

   <?php
   // your_site/Configuration/StyleSheets.php

   return [
       'frontend' => [
           'your-site-overrides' => [
               'source' => 'EXT:your_site/Resources/Public/StyleSheet/overrides.css',
           ],
       ],
   ];

TypoScript settings reference
------------------------------

The following ``settings`` keys are available inside all templates:

.. list-table::
   :widths: 35 15 50
   :header-rows: 1

   * - Key
     - Default
     - Purpose
   * - ``settings.site.name``
     - ``TYPO3 Site``
     - Site name shown in ``<title>`` and footer copyright
   * - ``settings.site.logoSrc``
     - *(empty)*
     - ``EXT:`` path to logo image; falls back to site name text
   * - ``settings.rootPageUid``
     - ``1``
     - UID used for the logo href
   * - ``settings.layout.sidebar``
     - ``0``
     - Set to ``1`` to enable the sidebar-layout wrapper around ``lib.content``
   * - ``settings.header.showActions``
     - ``0``
     - Set to ``1`` to render the ``Page/HeaderActions`` partial slot
   * - ``settings.footer.showNav``
     - ``1``
     - Set to ``0`` to hide the footer navigation partial
   * - ``settings.footer.columns``
     - ``0``
     - Set to ``1`` to render the ``Page/FooterColumns`` partial slot

Override in TypoScript:

.. code-block:: typoscript

   page.10.settings {
       site {
           name    = Acme Corp
           logoSrc = EXT:acme_site/Resources/Public/Icons/logo.svg
       }
       rootPageUid = 1
       layout.sidebar = 1
   }
