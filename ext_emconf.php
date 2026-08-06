<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Mai Theme',
    'description' => 'The **frontend component layer** for the entire extension set. All Fluid templates in feature extensions reference components defined here via native TYPO3 Fluid Components (Fluid 4.3+). `mai_theme` is a practical runtime requirement for every extension that renders frontend output.',
    'category' => 'module',
    'author' => 'Maispace',
    'author_email' => '',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.99.99',
            'mai_turbo' => '14.0.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
