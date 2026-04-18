<?php

declare(strict_types=1);

/**
 * content-defender restriction for section containers.
 *
 * Child CType allow-list per colPos. Placing a disallowed CType inside
 * a section container column is rejected by `ichhabrecht/content-defender`.
 */
return [
    'maispace_section_light'       => ['allowed' => ['CType' => '*']],
    'maispace_section_muted'       => ['allowed' => ['CType' => '*']],
    'maispace_section_dark'        => ['allowed' => ['CType' => '*']],
    'maispace_section_accent'      => ['allowed' => ['CType' => '*']],
    'maispace_section_transparent' => ['allowed' => ['CType' => '*']],
    'maispace_section_sidebar_r'   => ['allowed' => ['CType' => '*']],
    'maispace_section_sidebar_l'   => ['allowed' => ['CType' => '*']],
];
