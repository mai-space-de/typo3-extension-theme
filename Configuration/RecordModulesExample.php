<?php

/**
 * Example RecordModules configuration.
 *
 * Create a file named RecordModules.php in the Configuration folder
 * of your extension and return an array keyed by TCA table name.
 *
 * Each entry configures a dedicated backend module that shows a filtered
 * record list for that table.
 *
 * return [
 *     'sys_category' => [
 *         // (optional) Comma-separated page IDs to restrict the listing.
 *         // Omit or leave empty to show the page tree for selection.
 *         'pids' => '1,42',
 *
 *         // (optional) Integer sort order — lower values appear first.
 *         'sorting' => 10,
 *
 *         // (optional) Module title. Defaults to the TCA table title.
 *         // Supports LLL: references.
 *         'title' => 'LLL:EXT:your_ext/Resources/Private/Language/locallang.xlf:categories',
 *
 *         // (optional) Path to an icon file (EXT: syntax).
 *         // Defaults to the TCA ctrl.iconfile / typeicon_classes.
 *         'icon' => 'EXT:your_ext/Resources/Public/Icons/categories.svg',
 *
 *         // (optional) Registered icon identifier (alternative to 'icon').
 *         'iconIdentifier' => 'my-ext-categories-icon',
 *
 *         // (optional) Parent module identifier.
 *         // Defaults to 'theme_records' (a custom "Records" group).
 *         // Use 'web' to place the module under the Web group.
 *         'parent' => 'web',
 *     ],
 * ];
 */
