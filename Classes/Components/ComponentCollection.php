<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Components;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\Core\Component\AbstractComponentCollection;
use TYPO3Fluid\Fluid\View\TemplatePaths;

/**
 * Mai_theme component collection — registers `theme` Fluid namespace globally.
 * Uses flat folder structure: Atom/Button.fluid.html (not Atom/Button/Button.fluid.html).
 */
final class ComponentCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('mai_theme', 'Resources/Private/Components'),
        ]);
        return $templatePaths;
    }

    /** Converts "atom.button" → "Atom/Button" (flat structure, not nested folders). */
    public function resolveTemplateName(string $viewHelperName): string
    {
        $fragments = array_map(ucfirst(...), explode('.', $viewHelperName));
        return implode('/', $fragments);
    }
}
