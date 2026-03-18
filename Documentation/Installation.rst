.. include:: /Documentation/Includes.rst.txt

.. _installation:

Installation
============

Requirements
------------

* TYPO3 13.4 LTS
* PHP 8.2 or higher
* `maispace/assets`_ ^13.0 (provides server-side SCSS compilation)
* `sitegeist/fluid-components`_ ^3.0 (provides the ``fc:component`` ViewHelper)

.. _maispace/assets: https://github.com/mai-space-de/typo3-extension-assets
.. _sitegeist/fluid-components: https://github.com/sitegeist/fluid-components

Composer
--------

.. code-block:: bash

   composer require maispace/theme

This will also pull in ``maispace/assets`` and ``sitegeist/fluid-components`` as dependencies.

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

Site set (recommended)
----------------------

Add the ``maispace/theme-base`` site set as a dependency of your site to
unlock all theme settings in the TYPO3 backend settings editor (logos, colors,
typography, layout):

.. code-block:: yaml

   # config/sites/my-site/config.yaml
   sets:
     - maispace/theme-base

Once the set is active, open :guilabel:`Site Management > Sites > <your site> >
Settings` to configure the theme. All settings take effect after clearing the
page cache — no SCSS recompilation required.

See :ref:`site-set` for the complete reference of all available settings and a
detailed explanation of how they flow into TypoScript, Fluid, and CSS.

Backend theme via site settings
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

When the ``maispace/theme-base`` site set is active, backend logos and login-page
customisation are configured through the same settings editor under the
:guilabel:`Backend Theme` category. No PHP file is required.

.. code-block:: yaml

   # config/sites/my-site/settings.yaml (managed via TYPO3 backend)
   maispace.theme.backend.loginLogo: 'EXT:my_site/Resources/Public/Images/login-logo.png'
   maispace.theme.backend.loginHighlightColor: '#e11d48'
   maispace.theme.backend.loginFootnote: '© 2026 Acme Corp'

Backend theme via PHP (alternative)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For existing installations or multi-site setups that need an explicit global
backend theme, continue using ``Configuration/BackendTheme.php``. Call
``BackendTheme::registerBackendTheme()`` inside your site package's
``ext_localconf.php``:

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
       'backendLogo'          => 'EXT:your_extension/Resources/Public/Icons/logo.svg',
       'backendFavicon'       => 'EXT:your_extension/Resources/Public/Icons/favicon.ico',
       'loginLogo'            => 'EXT:your_extension/Resources/Public/Images/login-logo.png',
       'loginLogoAlt'         => 'My Project',
       'loginBackgroundImage' => 'EXT:your_extension/Resources/Public/Images/login-bg.jpg',
       'loginHighlightColor'  => '#2563eb',
       'loginFootnote'        => '© 2026 My Company',
   ];

See :ref:`configuration` for the complete list of available backend theme
settings.

Record Modules (optional)
-------------------------

Register dedicated backend modules for specific TCA record types by creating
``Configuration/RecordModules.php`` in your extension:

.. code-block:: php

   <?php
   // your_extension/Configuration/RecordModules.php

   return [
       'sys_category' => [
           'pids'    => '1,42',
           'sorting' => 10,
           'title'   => 'Categories',
           'parent'  => 'web',
       ],
       'tx_news_domain_model_news' => [
           'pids'    => '5',
           'sorting' => 20,
       ],
   ];

No additional setup is needed — ``EXT:theme`` automatically discovers
``RecordModules.php`` files from all active extensions and registers the
corresponding backend modules. Each entry creates a sidebar module that
shows a filtered record list for the configured table.

See :ref:`configuration` for the full list of available fields (``pids``,
``sorting``, ``title``, ``icon``, ``iconIdentifier``, ``parent``).

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
