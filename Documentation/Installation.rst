.. include:: /Documentation/Includes.rst.txt

.. _installation:

Installation
============

Requirements
------------

* TYPO3 13.4 LTS
* PHP 8.2 or higher
* `maispace/assets`_ ^1.0 (provides server-side SCSS compilation)

.. _maispace/assets: https://github.com/mai-space-de/typo3-extension-assets

Composer
--------

.. code-block:: bash

   composer require maispace/theme

This will also pull in ``maispace/assets`` as a dependency.

TypoScript
----------

Import the ``maispace/assets`` setup first, then the theme setup. Both imports
belong in your site package's TypoScript *setup* file:

.. code-block:: typoscript

   # Site package — Configuration/TypoScript/setup.typoscript
   @import 'EXT:maispace_assets/Configuration/TypoScript/setup.typoscript'
   @import 'EXT:theme/Configuration/TypoScript/setup.typoscript'

The theme setup registers the ``page = PAGE`` object with the default Fluid
template. If you already define your own ``page`` object, import the theme
setup before it so that your page object takes precedence.

Backend theme (optional)
------------------------

Call ``BackendTheme::registerBackendTheme()`` inside your site package's
``ext_localconf.php`` to apply backend logo and login-page settings:

.. code-block:: php

   <?php
   // your_extension/ext_localconf.php

   use Maispace\Theme\Services\BackendTheme;
   use TYPO3\CMS\Core\Utility\GeneralUtility;

   GeneralUtility::makeInstance(BackendTheme::class)->registerBackendTheme();

Then create ``Configuration/BackendTheme.php`` in your extension:

.. code-block:: php

   <?php
   // your_extension/Configuration/BackendTheme.php

   return [
       'backendLogo'        => 'EXT:your_extension/Resources/Public/Icons/logo.svg',
       'backendFavicon'     => 'EXT:your_extension/Resources/Public/Icons/favicon.ico',
       'loginBackgroundImage' => 'EXT:your_extension/Resources/Public/Images/login-bg.jpg',
       'loginHighlightColor'  => '#2563eb',
   ];

Overriding the stylesheet bundle
---------------------------------

The ITCSS bundle is compiled by ``<mai:scss>`` inside the base layout
(``EXT:theme/Resources/Private/Layouts/Page/Default.html``). SCSS compilation
and registration are both handled by ``maispace/assets`` — no PHP registration
code in the theme extension is involved.

To customise the design tokens without touching the source files, override CSS
custom properties in ``:root``:

.. code-block:: css

   /* your_extension/Resources/Public/StyleSheet/overrides.css */

   :root {
       --color-primary:    #e11d48;   /* rose-600 */
       --color-accent:     #f59e0b;
       --font-family-base: 'Inter', system-ui, sans-serif;
       --layout-radius:    0.25rem;
   }

Register the override file in ``Configuration/StyleSheets.php`` of your
extension — ``EXT:theme``'s ``FrontendAssetConfigurationsListener`` picks it
up automatically from any active package:

.. code-block:: php

   <?php
   // your_extension/Configuration/StyleSheets.php

   return [
       'frontend' => [
           'my-overrides' => [
               'source' => 'EXT:your_extension/Resources/Public/StyleSheet/overrides.css',
           ],
       ],
   ];

Adding your own SCSS
~~~~~~~~~~~~~~~~~~~~~

Because the theme TypoScript registers its ``StyleSheets/`` directory as a
``defaultImportPaths`` entry for ``maispace/assets``, you can import theme
partials in your own SCSS without specifying full ``EXT:`` paths:

.. code-block:: scss

   // your_extension/Resources/Private/StyleSheets/site.scss
   @use "02-mixins/media-breakpoint" as *;
   @use "01-settings/variables";

   .my-component {
       @include bp-up(lg) {
           display: flex;
       }
   }

Include it via ``<mai:scss>`` in your overridden layout or partial:

.. code-block:: html

   <mai:scss
       src="EXT:your_extension/Resources/Private/StyleSheets/site.scss"
       identifier="your-extension-scss"
       priority="2"
   />
