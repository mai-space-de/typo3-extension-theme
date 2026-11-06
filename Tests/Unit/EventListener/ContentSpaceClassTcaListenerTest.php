<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Unit\EventListener;

use Maispace\MaiTheme\EventListener\ContentSpaceClassTcaListener;
use PHPUnit\Framework\TestCase;

final class ContentSpaceClassTcaListenerTest extends TestCase
{
    public function testItemsAreNonePlusSpacingTokens(): void
    {
        $tca = (new ContentSpaceClassTcaListener())->apply($this->baseTca());
        $before = $tca['tt_content']['columns']['space_before_class']['config']['items'];
        $after = $tca['tt_content']['columns']['space_after_class']['config']['items'];

        self::assertSame($before, $after);
        self::assertSame('', $before[0]['value']);
        self::assertStringEndsWith(':spacing.keiner', $before[0]['label']);
        self::assertSame(
            ContentSpaceClassTcaListener::TOKENS,
            array_column(array_slice($before, 1), 'value'),
        );
    }

    public function testFramesPaletteIsNotDuplicated(): void
    {
        $tca = $this->baseTca();
        $tca['tt_content']['types']['header'] = [
            'showitem' => '--div--;core.form.tabs:appearance, --palette--;;frames',
        ];

        $result = (new ContentSpaceClassTcaListener())->apply($tca);

        self::assertSame(
            '--div--;core.form.tabs:appearance, --palette--;;frames',
            $result['tt_content']['types']['header']['showitem'],
        );
    }

    public function testThemeSpacingPaletteReceivesBothFieldsOnce(): void
    {
        $tca = $this->baseTca();
        $tca['tt_content']['palettes']['maispace_spacing'] = [
            'showitem' => 'tx_maitheme_margin_top, tx_maitheme_margin_bottom',
        ];
        $tca['tt_content']['types']['maispace_text'] = [
            'showitem' => '--palette--;;maispace_spacing',
        ];

        $result = (new ContentSpaceClassTcaListener())->apply($tca);

        self::assertSame(
            'space_before_class, space_after_class, --linebreak--, tx_maitheme_margin_top, tx_maitheme_margin_bottom',
            $result['tt_content']['palettes']['maispace_spacing']['showitem'],
        );
        self::assertSame(
            '--palette--;;maispace_spacing',
            $result['tt_content']['types']['maispace_text']['showitem'],
        );
    }

    public function testTypeWithoutSpacingFieldsGetsBoth(): void
    {
        $tca = $this->baseTca();
        $tca['tt_content']['types']['mai_events_view'] = [
            'showitem' => '--palette--;;general, header',
        ];

        $result = (new ContentSpaceClassTcaListener())->apply($tca);

        self::assertStringContainsString(
            'space_before_class, space_after_class',
            $result['tt_content']['types']['mai_events_view']['showitem'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function baseTca(): array
    {
        return [
            'tt_content' => [
                'columns' => [
                    'space_before_class' => ['config' => ['items' => [['label' => 'old', 'value' => 'small']]]],
                    'space_after_class' => ['config' => ['items' => [['label' => 'old', 'value' => 'small']]]],
                ],
                'types' => [],
            ],
        ];
    }
}
