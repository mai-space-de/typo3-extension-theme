<?php
declare(strict_types=1);

namespace Maispace\Theme\EventListener;

use Closure;
use Maispace\Theme\Services\ActiveExtensionConfigurationLoader;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Page\Event\BeforeStylesheetsRenderingEvent;
use TYPO3\CMS\Core\Page\Event\BeforeJavaScriptsRenderingEvent;
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
        $this->serverRequest = $GLOBALS['TYPO3_REQUEST'];
    }

    public function setStyleSheetConfiguration(BeforeStylesheetsRenderingEvent $event): void
    {
        $this->registerAssetsByType($event, self::ASSET_TYPE_STYLESHEET);
    }

    public function setJavaScriptConfiguration(BeforeJavaScriptsRenderingEvent $event): void
    {
        $this->registerAssetsByType($event, self::ASSET_TYPE_JAVASCRIPT);
    }

    /**
     * @param BeforeStylesheetsRenderingEvent|BeforeJavaScriptsRenderingEvent $event
     * @param string $type
     * @return void
     */
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

    private function getAssetsForCurrentSite(string $siteIdentifier, ApplicationType $applicationType, string $assetType): array
    {
        $assets = GeneralUtility::makeInstance(ActiveExtensionConfigurationLoader::class)
            ->getMergedConfigurationByFilename($assetType);

        if ($applicationType->isFrontend()) {
            $assets = $assets[self::APPLICATION_TYPE_FRONTEND] ?? [];
        } elseif ($applicationType->isBackend()) {
            return $assets[self::APPLICATION_TYPE_BACKEND] ?? [];
        }

        return array_filter($assets, static function ($asset) use ($siteIdentifier) {
            if (array_key_exists('site-identifier', $asset)) {
                return $asset['site-identifier'] === $siteIdentifier;
            }

            return true;
        });
    }

    /**
     * @param AssetCollector $assetCollector
     * @return Closure
     */
    private function getRegisterClosure(AssetCollector $assetCollector): Closure
    {
        return static function (string $type, string $assetIdentifier, array $asset) use ($assetCollector) {
            if ($type === self::ASSET_TYPE_STYLESHEET) {
                $assetCollector->addStyleSheet(
                    $assetIdentifier,
                    $asset['source'],
                    $asset['attributes'] ?? [],
                    $asset['options'] ?? []
                );
            } elseif ($type === self::ASSET_TYPE_JAVASCRIPT) {
                $assetCollector->addJavaScript(
                    $assetIdentifier,
                    $asset['source'],
                    $asset['attributes'] ?? [],
                    $asset['options'] ?? []
                );
            }
        };
    }
}