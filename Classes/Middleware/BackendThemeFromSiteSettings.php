<?php

declare(strict_types = 1);

namespace Maispace\Theme\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Applies backend theme settings (logos, login-page colors, footnote) from
 * the TYPO3 site settings of the maispace/theme-base site set.
 *
 * Because the TYPO3 backend is a global interface, backend theming is not
 * per-site. This middleware picks the first site that has a non-empty
 * `maispace.theme.backend.loginLogo` setting and uses that site's backend
 * theme values. For single-site installations this always resolves to the
 * one configured site.
 *
 * For multi-site setups where each site has a different backend logo
 * configured, the site whose identifier comes first alphabetically wins.
 * In that case, use Configuration/BackendTheme.php instead for explicit
 * global control (it is merged in by BackendTheme::registerBackendTheme()
 * and processed independently of this middleware).
 *
 * Settings read (all from the maispace/theme-base site set):
 *   maispace.theme.backend.logo              → backendLogo
 *   maispace.theme.backend.favicon           → backendFavicon
 *   maispace.theme.backend.loginLogo         → loginLogo
 *   maispace.theme.backend.loginLogoAlt      → loginLogoAlt
 *   maispace.theme.backend.loginBackground   → loginBackgroundImage
 *   maispace.theme.backend.loginHighlightColor → loginHighlightColor
 *   maispace.theme.backend.loginFootnote     → loginFootnote
 */
final class BackendThemeFromSiteSettings implements MiddlewareInterface
{
    /**
     * Maps site setting identifiers to $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend'] keys.
     *
     * @var array<string, string>
     */
    private const SETTING_MAP = [
        'maispace.theme.backend.logo'                => 'backendLogo',
        'maispace.theme.backend.favicon'             => 'backendFavicon',
        'maispace.theme.backend.loginLogo'           => 'loginLogo',
        'maispace.theme.backend.loginLogoAlt'        => 'loginLogoAlt',
        'maispace.theme.backend.loginBackground'     => 'loginBackgroundImage',
        'maispace.theme.backend.loginHighlightColor' => 'loginHighlightColor',
        'maispace.theme.backend.loginFootnote'       => 'loginFootnote',
    ];

    public function __construct(
        private readonly SiteFinder $siteFinder,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $this->applyBackendThemeFromSiteSettings();
        } catch (\Throwable) {
            // Never break the backend if site settings are unavailable or
            // the SiteFinder throws (e.g. during initial installation).
        }

        return $handler->handle($request);
    }

    private function applyBackendThemeFromSiteSettings(): void
    {
        $sites = $this->siteFinder->getAllSites();

        if (empty($sites)) {
            return;
        }

        // Use the first site that has an explicit loginLogo configured.
        // If no site has a loginLogo set, no backend theming is applied.
        $targetSite = null;
        foreach ($sites as $site) {
            $siteSettings = $site->getSettings();
            $loginLogoRaw = $siteSettings->get('maispace.theme.backend.loginLogo');
            $loginLogo = is_string($loginLogoRaw) ? $loginLogoRaw : '';
            if ($loginLogo !== '') {
                $targetSite = $site;
                break;
            }
        }

        if ($targetSite === null) {
            return;
        }

        $siteSettings = $targetSite->getSettings();
        $typo3ConfVars = (array)($GLOBALS['TYPO3_CONF_VARS'] ?? []);
        $extensions = (array)($typo3ConfVars['EXTENSIONS'] ?? []);
        $backend = (array)($extensions['backend'] ?? []);

        foreach (self::SETTING_MAP as $settingKey => $confVarsKey) {
            $rawValue = $siteSettings->get($settingKey);
            $value = is_string($rawValue) ? $rawValue : '';
            if ($value === '') {
                continue;
            }

            $backend[$confVarsKey] = $value;
        }

        $extensions['backend'] = $backend;
        $typo3ConfVars['EXTENSIONS'] = $extensions;
        $GLOBALS['TYPO3_CONF_VARS'] = $typo3ConfVars;
    }
}
