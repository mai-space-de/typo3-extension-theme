.. include:: /Documentation/Includes.rst.txt

.. _configuration:

Configuration
=============

``EXT:theme`` automatically discovers configuration files from all active TYPO3
packages. Place a PHP file with the appropriate name in your extension's
``Configuration/`` directory and it will be loaded and merged at runtime.

.. contents:: On this page
   :local:
   :depth: 2

How auto-discovery works
------------------------

The ``ActiveExtensionConfigurationLoader`` service scans every active TYPO3
package for a file matching ``Configuration/{Name}.php``. Each file must return
a PHP array. When the same filename exists in multiple extensions the arrays are
merged recursively — later packages override earlier ones.

Four filenames are used by the theme extension out of the box:

.. list-table::
   :widths: 25 75
   :header-rows: 1

   * - Filename
     - Purpose
   * - ``StyleSheets.php``
     - Register CSS / SCSS files for automatic inclusion via ``AssetCollector``
   * - ``JavaScripts.php``
     - Register JavaScript files for automatic inclusion via ``AssetCollector``
   * - ``BackendTheme.php``
     - Customise the TYPO3 backend login page and logo
   * - ``RecordModules.php``
     - Register dedicated backend modules for specific record types

The auto-discovery is driven by the
``FrontendAssetConfigurationsListener`` event listener (for stylesheets and
JavaScripts), the ``BackendTheme`` service (for backend theming) and the
``RecordModuleRegistrar`` service (for record modules). Both asset and backend
theme services are registered in ``Configuration/Services.yaml`` and
``ext_localconf.php`` respectively. Record modules are registered dynamically
via ``Configuration/Backend/Modules.php``.

StyleSheets.php
---------------

Register stylesheets for the frontend and/or backend. Each entry is passed to
TYPO3's ``AssetCollector``.

.. code-block:: php

   <?php
   // your_extension/Configuration/StyleSheets.php

   return [
       'frontend' => [
           'my-custom-styles' => [
               'source'          => 'EXT:your_extension/Resources/Public/Css/style.css',
               'site-identifier' => 'my_site',   // Optional: only include for this site
               'attributes'      => [],           // Optional: HTML attributes for the <link> tag
               'options'         => [],           // Optional: AssetCollector options
           ],
       ],
       'backend' => [
           'my-backend-styles' => [
               'source'     => 'EXT:your_extension/Resources/Public/Css/backend.css',
               'attributes' => [],
               'options'    => [],
           ],
       ],
   ];

**Fields:**

.. list-table::
   :widths: 20 10 70
   :header-rows: 1

   * - Key
     - Required
     - Description
   * - ``source``
     - yes
     - Path to the CSS file, typically an ``EXT:`` path.
   * - ``site-identifier``
     - no
     - When set, the stylesheet is only included if the current TYPO3 site
       identifier matches this value. Only applies to ``frontend`` entries.
   * - ``attributes``
     - no
     - Array of HTML attributes added to the ``<link>`` tag
       (e.g. ``['media' => 'print']``).
   * - ``options``
     - no
     - Array of ``AssetCollector`` options
       (e.g. ``['priority' => true]``).

Frontend entries support the ``site-identifier`` filter. Backend entries are
always included when the TYPO3 backend is loaded.

JavaScripts.php
---------------

Register JavaScript files for the frontend and/or backend. The structure
mirrors ``StyleSheets.php``.

.. code-block:: php

   <?php
   // your_extension/Configuration/JavaScripts.php

   return [
       'frontend' => [
           'my-custom-script' => [
               'source'          => 'EXT:your_extension/Resources/Public/Js/app.js',
               'site-identifier' => 'my_site',
               'attributes'      => ['defer' => 'defer'],
               'options'         => [],
           ],
       ],
       'backend' => [
           'my-backend-script' => [
               'source'     => 'EXT:your_extension/Resources/Public/Js/backend.js',
               'attributes' => [],
               'options'    => [],
           ],
       ],
   ];

**Fields:**

.. list-table::
   :widths: 20 10 70
   :header-rows: 1

   * - Key
     - Required
     - Description
   * - ``source``
     - yes
     - Path to the JavaScript file, typically an ``EXT:`` path.
   * - ``site-identifier``
     - no
     - When set, the script is only included if the current TYPO3 site
       identifier matches this value. Only applies to ``frontend`` entries.
   * - ``attributes``
     - no
     - Array of HTML attributes added to the ``<script>`` tag
       (e.g. ``['defer' => 'defer', 'async' => 'async']``).
   * - ``options``
     - no
     - Array of ``AssetCollector`` options
       (e.g. ``['priority' => true]``).

BackendTheme.php
----------------

Customise the TYPO3 backend login page appearance and backend logo. These
settings map directly to ``$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']``.

.. code-block:: php

   <?php
   // your_extension/Configuration/BackendTheme.php

   return [
       'backendLogo'          => 'EXT:your_extension/Resources/Public/Backend/Logo.svg',
       'backendFavicon'       => 'EXT:your_extension/Resources/Public/Backend/Favicon.ico',
       'loginLogo'            => 'EXT:your_extension/Resources/Public/Backend/LoginLogo.png',
       'loginLogoAlt'         => 'My Project',
       'loginBackgroundImage' => 'EXT:your_extension/Resources/Public/Backend/LoginBackground.jpg',
       'loginHighlightColor'  => '#6272A4',
       'loginFootnote'        => '© 2026 My Company',
   ];

**Available settings:**

.. list-table::
   :widths: 25 75
   :header-rows: 1

   * - Key
     - Description
   * - ``backendLogo``
     - Logo displayed in the top-left of the TYPO3 backend.
   * - ``backendFavicon``
     - Favicon used in the backend browser tab.
   * - ``loginLogo``
     - Logo shown on the backend login page.
   * - ``loginLogoAlt``
     - Alt text for the login logo image.
   * - ``loginBackgroundImage``
     - Background image on the backend login page.
   * - ``loginHighlightColor``
     - Accent colour used on the login page (e.g. button colour).
   * - ``loginFootnote``
     - Text displayed below the login form (e.g. copyright notice).

.. important::

   ``BackendTheme::registerBackendTheme()`` must be called in your site
   package's ``ext_localconf.php`` for the settings to take effect. See
   :ref:`installation` for details.

RecordModules.php
-----------------

Register dedicated backend modules that show a filtered record list for
specific TCA tables. Editors can access records directly without navigating
through the generic List module.

.. code-block:: php

   <?php
   // your_extension/Configuration/RecordModules.php

   return [
       'sys_category' => [
           'pids'    => '1,42',
           'sorting' => 10,
           'title'   => 'LLL:EXT:your_extension/Resources/Private/Language/locallang.xlf:categories',
           'icon'    => 'EXT:your_extension/Resources/Public/Icons/categories.svg',
           'parent'  => 'web',
       ],
       'tx_news_domain_model_news' => [
           'pids'    => '5',
           'sorting' => 20,
       ],
   ];

**Fields:**

.. list-table::
   :widths: 20 10 70
   :header-rows: 1

   * - Key
     - Required
     - Description
   * - ``pids``
     - no
     - Comma-separated page IDs or an array of IDs to restrict the record
       listing. When omitted the TYPO3 page tree is shown so the editor can
       select a page.
   * - ``sorting``
     - no
     - Integer sort order. Modules with lower values appear first.
       Defaults to ``9999``.
   * - ``title``
     - no
     - Module title shown in the backend sidebar. Defaults to the TCA table
       title. Supports ``LLL:`` references.
   * - ``icon``
     - no
     - Path to an icon file (``EXT:`` syntax). Defaults to the icon configured
       in the TCA ``ctrl.iconfile`` or ``ctrl.typeicon_classes``.
   * - ``iconIdentifier``
     - no
     - Registered icon identifier (alternative to ``icon``).
   * - ``parent``
     - no
     - Parent module identifier. Defaults to ``theme_records`` which creates a
       custom "Records" module group. Use ``web`` to place the module under the
       Web group.

Multi-extension merging
-----------------------

When multiple extensions provide the same configuration filename, the arrays
are merged recursively using ``ArrayUtility::mergeRecursiveWithOverrule()``.
Extensions are processed in the order returned by the TYPO3 ``PackageManager``,
so later extensions override earlier ones.

For asset configuration this means that a site package loaded after the theme
extension can override or extend the asset list. For backend theme settings the
last value wins.
