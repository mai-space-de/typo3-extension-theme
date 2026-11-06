<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;

/**
 * Puts space_before_class and space_after_class on every content type and
 * limits their options to "Keiner" plus the spacing design tokens.
 *
 * Runs after TCA compilation so content types registered by other extensions
 * are included. Types that already show the fields through the core frames
 * palette or the theme spacing palette are left untouched.
 */
#[AsEventListener(identifier: 'mai-theme/content-space-classes')]
final class ContentSpaceClassTcaListener
{
    private const string LABEL_PREFIX = 'LLL:EXT:mai_theme/Resources/Private/Language/Default/locallang_tca.xlf:';

    /**
     * Layout spacing scale from 01-settings/_variables.scss.
     * The em-based tokens stay out of this list; they are type-relative.
     *
     * @var list<string>
     */
    public const array TOKENS = ['2xs', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl'];

    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $event->setTca($this->apply($event->getTca()));
    }

    /**
     * @param array<string, mixed> $tca
     * @return array<string, mixed>
     */
    public function apply(array $tca): array
    {
        if (!isset($tca['tt_content']['columns']['space_before_class'], $tca['tt_content']['columns']['space_after_class'])) {
            return $tca;
        }

        $items = $this->items();
        $tca['tt_content']['columns']['space_before_class']['config']['items'] = $items;
        $tca['tt_content']['columns']['space_after_class']['config']['items'] = $items;

        $palette = (string) ($tca['tt_content']['palettes']['maispace_spacing']['showitem'] ?? '');
        if ($palette !== '' && !str_contains($palette, 'space_before_class')) {
            $tca['tt_content']['palettes']['maispace_spacing']['showitem'] = 'space_before_class, space_after_class, --linebreak--, ' . $palette;
        }

        $types = $tca['tt_content']['types'] ?? [];
        if (!is_array($types)) {
            return $tca;
        }

        foreach ($types as $typeName => $typeConfig) {
            if (!is_array($typeConfig)) {
                continue;
            }
            $showitem = (string) ($typeConfig['showitem'] ?? '');
            if (trim($showitem) === '' || $this->alreadyShowsSpaceFields($showitem)) {
                continue;
            }
            $tca['tt_content']['types'][$typeName]['showitem'] = rtrim($showitem, " \n,")
                . ', --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance, space_before_class, space_after_class';
        }

        return $tca;
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function items(): array
    {
        $items = [
            ['label' => self::LABEL_PREFIX . 'spacing.keiner', 'value' => ''],
        ];
        foreach (self::TOKENS as $token) {
            $items[] = ['label' => self::LABEL_PREFIX . 'spacing.' . $token, 'value' => $token];
        }

        return $items;
    }

    private function alreadyShowsSpaceFields(string $showitem): bool
    {
        return str_contains($showitem, 'space_before_class')
            || str_contains($showitem, ';frames')
            || str_contains($showitem, ';maispace_spacing');
    }
}
