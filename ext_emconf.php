<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Mai Theme',
    'description' => 'The **frontend component layer** for the entire extension set. All Fluid templates in feature extensions reference components defined here via `sitegeist/fluid-components`. `sitegeist/fluid-components` is never declared outside this extension. `mai_theme` is a practical runtime requirement for every extension that renders frontend output.',
    'category' => 'module',
    'author' => 'Maispace',
    'author_email' => '',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
