<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Icon;

/**
 * Curated content icons (Phosphor fill SVGs) for Featurebox / Icon CEs.
 *
 * Files live in EXT:mai_theme/Resources/Public/Icons/Content/{identifier}.svg
 */
final class ContentIconCatalog
{
    public const ICON_PATH = 'EXT:mai_theme/Resources/Public/Icons/Content/';

    /**
     * identifier => human label (English source; TCA uses LLL when available).
     *
     * @var array<string, string>
     */
    public const ICONS = [
        'map-pin' => 'Map pin',
        'hand-heart' => 'Hand with heart',
        'user' => 'User',
        'user-circle' => 'User circle',
        'users-three' => 'Users',
        'calendar-blank' => 'Calendar',
        'chat-circle' => 'Chat',
        'book-open' => 'Book',
        'house' => 'House',
        'globe' => 'Globe',
        'envelope-simple' => 'Envelope',
        'newspaper' => 'Newspaper',
        'download-simple' => 'Download',
        'question' => 'Question',
        'heart' => 'Heart',
        'flower' => 'Flower',
        'check-circle' => 'Check',
        'info' => 'Info',
        'warning-circle' => 'Warning',
        'star' => 'Star',
        'phone' => 'Phone',
    ];

    public static function iconRegistryIdentifier(string $identifier): string
    {
        return 'mai-theme-content-icon-' . $identifier;
    }

    public static function sourcePath(string $identifier): string
    {
        return self::ICON_PATH . $identifier . '.svg';
    }

    /**
     * TCA select items with Icon API references for the backend dropdown.
     *
     * @return list<array{label: string, value: string, icon?: string}>
     */
    public static function tcaItems(): array
    {
        $items = [
            ['label' => '', 'value' => ''],
        ];

        foreach (self::ICONS as $identifier => $label) {
            $items[] = [
                'label' => 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:icon.' . str_replace('-', '_', $identifier),
                'value' => $identifier,
                'icon' => self::iconRegistryIdentifier($identifier),
            ];
        }

        return $items;
    }

    /**
     * @return array<string, array{provider: class-string, source: string}>
     */
    public static function iconRegistryEntries(): array
    {
        $entries = [];
        foreach (array_keys(self::ICONS) as $identifier) {
            $entries[self::iconRegistryIdentifier($identifier)] = [
                'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                'source' => self::sourcePath($identifier),
            ];
        }

        return $entries;
    }
}
