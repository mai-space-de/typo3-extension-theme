<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Functional benchmark tests for Fluid component rendering times.
 *
 * These tests bootstrap a real TYPO3 instance, create Fluid views
 * using the same template paths configured in TypoScript, and
 * measure wall-clock render times for the Section container
 * templates (SectionFull, Section5050, Section3366, BentoBox).
 *
 * Each test:
 *  1. Creates a Fluid view via the container's ViewFactoryInterface
 *  2. Assigns mock content-element data that matches the template
 *     expectations
 *  3. Warms the Fluid cache with one initial render
 *  4. Renders the template N times and records timing statistics
 */
final class FluidRenderingBenchmarkTest extends FunctionalTestCase
{
    /**
     * Maximum acceptable average render time per template in milliseconds.
     * Set generously to avoid flaky failures in CI — this is a baseline.
     */
    private const float MAX_AVG_RENDER_TIME_MS = 500.0;

    /**
     * Number of iterations for the timed benchmark.
     */
    private const int BENCHMARK_ITERATIONS = 50;

    /**
     * Core extensions required for mai_theme's Fluid rendering chain.
     */
    protected array $coreExtensionsToLoad = [
        'fluid_styled_content',
    ];

    /**
     * Extensions required by mai_theme.
     * mai_base is needed for ext_localconf.php (BackendThemeService).
     * We register its PSR-4 autoloading manually in setUp() because it is
     * not installed in the extension's own vendor/ directory.
     */
    protected array $testExtensionsToLoad = [
        'maispace/mai-theme',
        'packages/typo3-extension-base',
        'b13/container',
    ];

    protected function setUp(): void
    {
        // mai_base is not in the extension's vendor/ — register PSR-4
        // autoloading so ext_localconf.php can resolve its classes.
        $maiBaseClasses = ORIGINAL_ROOT . 'packages/typo3-extension-base/Classes/';
        if (is_dir($maiBaseClasses)) {
            $loader = require ORIGINAL_ROOT . 'packages/typo3-extension-theme/vendor/autoload.php';
            /** @var \Composer\Autoload\ClassLoader $loader */
            $loader->addPsr4('Maispace\\MaiBase\\', $maiBaseClasses);
        }

        parent::setUp();
    }

    /**
     * Returns the Section container template names and their CType identifiers.
     *
     * @return array<string, array{templateName: string, cType: string, colPosKey: string}>
     */
    public static function sectionTemplateProvider(): array
    {
        return [
            'SectionFull' => [
                'templateName' => 'SectionFull',
                'cType' => 'maispace_section_full',
                'colPosKey' => 'single',
            ],
            'Section5050' => [
                'templateName' => 'Section5050',
                'cType' => 'maispace_section_50_50',
                'colPosKey' => '50-50',
            ],
            'Section3366' => [
                'templateName' => 'Section3366',
                'cType' => 'maispace_section_33_66',
                'colPosKey' => '33-66',
            ],
            'BentoBox' => [
                'templateName' => 'BentoBox',
                'cType' => 'maispace_bento',
                'colPosKey' => 'bento',
            ],
        ];
    }

    /**
     * Each Section container template must render successfully with
     * mock data and produce valid output containing the element UID.
     */
    #[DataProvider('sectionTemplateProvider')]
    public function testTemplateRendersSuccessfully(
        string $templateName,
        string $cType,
        string $colPosKey,
    ): void {
        $view = $this->createSectionView($templateName);

        $output = $view->render($templateName);
        self::assertNotEmpty(
            trim($output),
            "{$templateName} rendered output must not be empty",
        );
        self::assertStringContainsString(
            'data-ce-uid="999"',
            $output,
            "{$templateName} must contain the data-ce-uid attribute with the element UID",
        );
    }

    /**
     * Benchmark wall-clock rendering time for each Section container.
     *
     * Renders each template BENCHMARK_ITERATIONS times after a warm-up
     * render and records min / max / average timing. Fails if the
     * average exceeds MAX_AVG_RENDER_TIME_MS.
     */
    #[DataProvider('sectionTemplateProvider')]
    public function testBenchmarkSectionRenderingTime(
        string $templateName,
        string $cType,
        string $colPosKey,
    ): void {
        $view = $this->createSectionView($templateName);

        $view->render($templateName);

        $times = [];
        for ($i = 0; $i < self::BENCHMARK_ITERATIONS; $i++) {
            $start = hrtime(true);
            $view->render($templateName);
            $times[] = (hrtime(true) - $start) / 1_000_000;
        }

        $avg = array_sum($times) / count($times);
        $min = min($times);
        $max = max($times);
        $p50 = $this->percentile($times, 50);
        $p95 = $this->percentile($times, 95);

        $report = sprintf(
            "\n[BENCHMARK] %s (%s, %s)\n"
            . "  Iterations: %d\n"
            . "  Avg:    %8.3f ms\n"
            . "  P50:    %8.3f ms\n"
            . "  P95:    %8.3f ms\n"
            . "  Min:    %8.3f ms\n"
            . "  Max:    %8.3f ms\n",
            $templateName,
            $cType,
            $colPosKey,
            self::BENCHMARK_ITERATIONS,
            $avg,
            $p50,
            $p95,
            $min,
            $max,
        );
        fwrite(STDERR, $report);

        self::assertLessThan(
            self::MAX_AVG_RENDER_TIME_MS,
            $avg,
            sprintf(
                '%s average render time (%.2f ms) exceeds limit of %.0f ms',
                $templateName,
                $avg,
                self::MAX_AVG_RENDER_TIME_MS,
            ),
        );
    }

    private function createSectionView(string $templateName): \TYPO3\CMS\Core\View\ViewInterface
    {
        $viewFactory = $this->getContainer()->get(ViewFactoryInterface::class);

        $viewData = new ViewFactoryData(
            templateRootPaths: [
                100 => 'EXT:mai_theme/Resources/Private/Templates/ContentElements/',
            ],
            partialRootPaths: [
                100 => 'EXT:mai_theme/Resources/Private/Partials/',
            ],
            layoutRootPaths: [
                100 => 'EXT:mai_theme/Resources/Private/Layouts/',
            ],
        );

        $view = $viewFactory->create($viewData);

        $view->assignMultiple([
            'data' => [
                'uid' => 999,
                'header' => 'Benchmark Section',
                'tx_maitheme_anchor_id' => '',
            ],
            'children_200' => [],
            'children_201' => [],
            'children_220' => [],
            'children_221' => [],
            'children_250' => [],
        ]);

        return $view;
    }

    /**
     * Calculate the p-th percentile from a list of values.
     *
     * Uses linear interpolation between neighbouring values (R-7).
     *
     * @param list<float> $sorted  data set (will be sorted in-place)
     * @param float       $percentile  0–100
     */
    private function percentile(array $data, float $percentile): float
    {
        if ($data === []) {
            return 0.0;
        }

        sort($data, SORT_NUMERIC);
        $count = count($data);

        if ($percentile <= 0.0) {
            return $data[0];
        }
        if ($percentile >= 100.0) {
            return $data[$count - 1];
        }

        $rank = ($percentile / 100.0) * ($count - 1);
        $lower = (int) floor($rank);
        $upper = (int) ceil($rank);

        if ($lower === $upper) {
            return $data[$lower];
        }

        $fraction = $rank - $lower;
        return $data[$lower] + ($data[$upper] - $data[$lower]) * $fraction;
    }
}
