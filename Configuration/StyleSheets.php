<?php

/**
 * StyleSheets.php — maispace/theme
 *
 * Registers the compiled stylesheet bundle for the frontend.
 * The SCSS is compiled server-side by maispace/assets via the <mai:scss>
 * ViewHelper in EXT:theme/Resources/Private/Layouts/Page/Default.html.
 *
 * This file is auto-discovered by the FrontendAssetConfigurationsListener
 * from EXT:theme and merged with configurations from all active packages.
 *
 * To load the theme stylesheet without using the Fluid template engine
 * (e.g. in a non-Fluid setup), add an entry here pointing to a pre-compiled
 * CSS file and remove the <mai:scss> call from the layout.
 *
 * @see EXT:theme/Classes/EventListener/FrontendAssetConfigurationsListener.php
 */

return [
    /*
     * Frontend stylesheets
     * ─────────────────────────────────────────────────────────────────────
     * The primary bundle is compiled via <mai:scss> inside the Fluid layout
     * (EXT:theme/Resources/Private/Layouts/Page/Default.html) and therefore
     * does not need a separate CSS entry here.
     *
     * Uncomment the entry below to register a pre-compiled CSS fallback,
     * or to include additional stylesheets alongside the SCSS-compiled bundle.
     */
    'frontend' => [
        // 'theme-bundle' => [
        //     'source' => 'EXT:theme/Resources/Public/StyleSheet/bundle.css',
        //     'attributes' => [],
        //     'options' => [],
        // ],
    ],

    /*
     * Backend stylesheets
     * ─────────────────────────────────────────────────────────────────────
     * Backend stylesheets are not compiled via Fluid — add pre-compiled CSS
     * here to style the TYPO3 backend using theme tokens.
     */
    'backend' => [],
];
