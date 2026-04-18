<?php

declare(strict_types=1);

/**
 * content-defender restriction for the bento container. Only the generic
 * text / textmedia CTypes may live inside a bento cell — everything else
 * is rejected at save time.
 */
return [
    'maispace_bento' => [
        'allowed' => [
            'CType' => 'maispace_text,maispace_textmedia',
        ],
    ],
];
