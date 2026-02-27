<?php

declare(strict_types = 1);

namespace Maispace\Theme\EventListener;

use Maispace\Theme\Services\ActiveExtensionConfigurationLoader;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Page\Event\BeforeJavaScriptsRenderingEvent;
use TYPO3\CMS\Core\Page\Event\BeforeStylesheetsRenderingEvent;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FrontendAssetConfigurationsListener
{
    private const string ASSET_TYPE_STYLESHEET = 'StyleSheets';
    private const string ASSET_TYPE_JAVASCRIPT = 'JavaScripts';
    private const string APPLICATION_TYPE_FRONTEND = 'frontend';
    private const string APPLICATION_TYPE_BACKEND = 'backend';
    private const string DEFAULT_SITE_IDENTIFIER = 'default';

    private ?ServerRequest $serverRequest;

    public function __construct()
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $this->serverRequest = $request instanceof ServerRequest ? $request : null;
    }

    public function setStyleSheetConfiguration(BeforeStylesheetsRenderingEvent $event): void
    {
        $this->registerAssetsByType($event, self::ASSET_TYPE_STYLESHEET);
    }

    public function setJavaScriptConfiguration(BeforeJavaScriptsRenderingEvent $event): void
    {
        $this->registerAssetsByType($event, self::ASSET_TYPE_JAVASCRIPT);
    }

    private function registerAssetsByType(BeforeStylesheetsRenderingEvent|BeforeJavaScriptsRenderingEvent $event, string $type): void
    {
        if (!$this->serverRequest instanceof ServerRequest || !$this->serverRequest->getAttribute('applicationType')) {
            return;
        }
        $site = $this->serverRequest->getAttribute('site');
        $siteIdentifier = self::DEFAULT_SITE_IDENTIFIER;
        if ($site instanceof Site) {
            $siteIdentifier = $site->getIdentifier();
        }
        $applicationContext = ApplicationType::fromRequest($this->serverRequest);
        $assetCollector = $event->getAssetCollector();
        $registerClosure = $this->getRegisterClosure($assetCollector);

        $assets = $this->getAssetsForCurrentSite($siteIdentifier, $applicationContext, $type);
        foreach ($assets as $assetIdentifier => $asset) {
            $registerClosure(
                $type,
                $assetIdentifier,
                $asset
            );
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getAssetsForCurrentSite(string $siteIdentifier, ApplicationType $applicationType, string $assetType): array
    {
        $assets = GeneralUtility::makeInstance(ActiveExtensionConfigurationLoader::class)
            ->getMergedConfigurationByFilename($assetType);

        if ($applicationType->isFrontend()) {
            $assets = (array)($assets[self::APPLICATION_TYPE_FRONTEND] ?? []);
        } elseif ($applicationType->isBackend()) {
            /** @var array<string, array<string, mixed>> $backendAssets */
            $backendAssets = (array)($assets[self::APPLICATION_TYPE_BACKEND] ?? []);

            return $backendAssets;
        }

        /** @var array<string, array<string, mixed>> $filteredAssets */
        $filteredAssets = array_filter($assets, static function ($asset) use ($siteIdentifier) {
            if (is_array($asset) && array_key_exists('site-identifier', $asset)) {
                return $asset['site-identifier'] === $siteIdentifier;
            }

            return true;
        });

        return $filteredAssets;
    }

    private function getRegisterClosure(AssetCollector $assetCollector): \Closure
    {
        return static function (string $type, string $assetIdentifier, array $asset) use ($assetCollector) {
            if ($type === self::ASSET_TYPE_STYLESHEET) {
                $assetCollector->addStyleSheet(
                    $assetIdentifier,
                    is_string($asset['source'] ?? null) ? $asset['source'] : '',
                    (array)($asset['attributes'] ?? []),
                    (array)($asset['options'] ?? [])
                );
            } elseif ($type === self::ASSET_TYPE_JAVASCRIPT) {
                $assetCollector->addJavaScript(
                    $assetIdentifier,
                    is_string($asset['source'] ?? null) ? $asset['source'] : '',
                    (array)($asset['attributes'] ?? []),
                    (array)($asset['options'] ?? [])
                );
            }
        };
    }
}
