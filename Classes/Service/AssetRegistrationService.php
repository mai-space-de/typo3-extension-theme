<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Service;

use Maispace\MaiAssets\Configuration\ExtensionConfiguration as AssetsExtensionConfiguration;
use Maispace\MaiAssets\Processing\MinificationProcessor;
use Maispace\MaiAssets\Processing\ScssProcessor;
use Maispace\MaiAssets\Traits\CacheKeyTrait;
use Maispace\MaiAssets\Traits\FileResolutionTrait;
use Maispace\MaiTheme\Exception\AssetRegistrationException;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Registers `mai_theme` asset entry points (main SCSS, component JS) via the
 * `mai_assets` pipeline.
 *
 * Mirrors the DI style of `mai_assets` services:
 *  - constructor injection only (no `GeneralUtility::makeInstance` in business logic),
 *  - reuses {@see FileResolutionTrait} and {@see CacheKeyTrait} from `mai_assets`,
 *  - delegates compilation to {@see ScssProcessor} / {@see MinificationProcessor},
 *  - throws typed {@see AssetRegistrationException} on failure.
 *
 * ViewHelpers `<mai:css>` and `<mai:js>` are the preferred Fluid-side API;
 * this service is the PHP-side entry point used by the theme bootstrap and
 * warmup commands.
 */
final class AssetRegistrationService implements SingletonInterface
{
    use FileResolutionTrait;
    use CacheKeyTrait;

    /**
     * Main SCSS entry compiled by `mai_assets` — SCSS detection happens inside
     * `ScssProcessor`, which is the single authority on SCSS compilation per
     * the project's architecture constraints (AGENTS.md).
     */
    public const MAIN_SCSS = 'EXT:mai_theme/Resources/Public/Scss/main.scss';

    public function __construct(
        private readonly ScssProcessor $scssProcessor,
        private readonly MinificationProcessor $minificationProcessor,
        private readonly AssetCollector $assetCollector,
        private readonly AssetsExtensionConfiguration $assetsConfiguration,
    ) {}

    /**
     * Register the theme's main stylesheet with the page. Compiles SCSS through
     * `mai_assets` and publishes the resulting CSS via {@see AssetCollector}.
     */
    public function registerMainStylesheet(string $source = self::MAIN_SCSS, string $identifier = 'mai-theme-main'): void
    {
        $absolutePath = $this->requireFile($source);

        $content = @file_get_contents($absolutePath);
        if ($content === false) {
            throw new AssetRegistrationException(
                sprintf('Unable to read theme stylesheet "%s".', $absolutePath),
                1_736_000_010
            );
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        if ($ext === 'scss' && $this->assetsConfiguration->isEnableScssProcessing()) {
            $content = $this->scssProcessor->process($content, $absolutePath);
        }

        if ($this->assetsConfiguration->isEnableMinification()) {
            $content = $this->minificationProcessor->process($content, $absolutePath);
        }

        $publicPath = PathUtility::getAbsoluteWebPath($absolutePath);
        $this->assetCollector->addStyleSheet($identifier, $publicPath, ['media' => 'all']);
    }

    /**
     * Register a JavaScript file with the page.
     */
    public function registerJavaScript(string $source, string $identifier, bool $defer = true): void
    {
        $absolutePath = $this->requireFile($source);
        $publicPath = PathUtility::getAbsoluteWebPath($absolutePath);
        $attributes = $defer ? ['defer' => 'defer'] : [];
        $this->assetCollector->addJavaScript($identifier, $publicPath, $attributes);
    }

    /**
     * Warmup hook — pre-compiles the main SCSS entry so the first FE request
     * is cache-served. Idempotent.
     */
    public function warmup(): void
    {
        $absolute = $this->requireFile(self::MAIN_SCSS);
        $content = (string)file_get_contents($absolute);
        $this->scssProcessor->process($content, $absolute);
    }
}
