<?php

declare(strict_types = 1);

namespace Maispace\Theme\Services;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class RecordModuleRegistrar
{
    private const CONFIGURATION_FILENAME = 'RecordModules';
    private const DEFAULT_PARENT = 'theme_records';
    private const DEFAULT_SORTING = 9999;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function buildModuleRegistrations(): array
    {
        $configuration = GeneralUtility::makeInstance(ActiveExtensionConfigurationLoader::class)
            ->getMergedConfigurationByFilename(self::CONFIGURATION_FILENAME);

        if ($configuration === []) {
            return [];
        }

        /** @var array<string, array<string, mixed>> $modules */
        $modules = [];
        $needsCustomModuleGroup = false;

        /** @var array<string, mixed> $tca */
        $tca = (array)($GLOBALS['TCA'] ?? []);

        foreach ($configuration as $table => $settings) {
            $table = (string)$table;
            if (!is_array($settings)) {
                continue;
            }

            /** @var array<string, mixed> $settings */
            $settings = $settings;

            if (!isset($tca[$table])) {
                continue;
            }

            $parent = isset($settings['parent']) && is_string($settings['parent']) && trim($settings['parent']) !== ''
                ? trim($settings['parent'])
                : self::DEFAULT_PARENT;

            if ($parent === self::DEFAULT_PARENT) {
                $needsCustomModuleGroup = true;
            }

            $sorting = isset($settings['sorting']) && is_int($settings['sorting'])
                ? $settings['sorting']
                : self::DEFAULT_SORTING;

            $title = $this->resolveTitle($table, $settings, $tca);
            $pids = $this->resolvePids($settings);

            $navigationComponent = '';
            if ($pids === []) {
                $navigationComponent = '@typo3/backend/page-tree/page-tree-element';
            }

            $identifier = 'theme_recordmodule_' . $table;
            /** @var array<string, mixed> $moduleConfiguration */
            $moduleConfiguration = [
                'parent'     => $parent,
                'position'   => ['before' => 'web_list'],
                'sorting'    => $sorting,
                'access'     => 'user',
                'workspaces' => 'live',
                'path'       => '/module/' . $identifier,
                'labels'     => [
                    'title' => $title,
                ],
                'extensionName'                            => 'Theme',
                'navigationComponent'                      => $navigationComponent,
                'inheritNavigationComponentFromMainModule' => false,
                'routes'                                   => [
                    '_default' => [
                        'target' => \Maispace\Theme\Controller\RecordModuleController::class . '::mainAction',
                    ],
                ],
                'moduleData' => [
                    'table'     => $table,
                    'title'     => $title,
                    'pids'      => $pids,
                    'clipBoard' => true,
                    'searchBox' => true,
                ],
            ];

            $this->applyIcon($moduleConfiguration, $table, $settings, $tca);

            $modules[$identifier] = $moduleConfiguration;
        }

        if ($modules !== []) {
            uasort($modules, static function (array $a, array $b): int {
                $sortA = is_int($a['sorting'] ?? null) ? $a['sorting'] : self::DEFAULT_SORTING;
                $sortB = is_int($b['sorting'] ?? null) ? $b['sorting'] : self::DEFAULT_SORTING;

                return $sortA <=> $sortB;
            });
        }

        if ($needsCustomModuleGroup) {
            $modules[self::DEFAULT_PARENT] = [
                'labels'              => 'LLL:EXT:theme/Resources/Private/Language/locallang_record_module.xlf',
                'iconIdentifier'      => 'actions-brand-typo3',
                'extensionName'       => 'Theme',
                'position'            => ['after' => 'web'],
                'navigationComponent' => '',
            ];
        }

        return $modules;
    }

    /**
     * @param array<string, mixed> $settings
     * @param array<string, mixed> $tca
     */
    private function resolveTitle(string $table, array $settings, array $tca): string
    {
        if (isset($settings['title']) && is_string($settings['title']) && trim($settings['title']) !== '') {
            return trim($settings['title']);
        }

        $tableConfig = is_array($tca[$table] ?? null) ? $tca[$table] : [];
        $ctrl = is_array($tableConfig['ctrl'] ?? null) ? $tableConfig['ctrl'] : [];

        return is_string($ctrl['title'] ?? null) ? $ctrl['title'] : $table;
    }

    /**
     * @param array<string, mixed> $settings
     *
     * @return list<int>
     */
    private function resolvePids(array $settings): array
    {
        if (!isset($settings['pids'])) {
            return [];
        }

        if (is_array($settings['pids'])) {
            $pids = array_map(static fn (mixed $v): int => is_numeric($v) ? (int)$v : 0, $settings['pids']);

            return array_values(array_filter($pids, static fn (int $pid): bool => $pid > 0));
        }

        if (is_string($settings['pids']) || is_int($settings['pids'])) {
            return GeneralUtility::intExplode(',', (string)$settings['pids'], true);
        }

        return [];
    }

    /**
     * @param array<string, mixed> $moduleConfiguration
     * @param array<string, mixed> $settings
     * @param array<string, mixed> $tca
     */
    private function applyIcon(array &$moduleConfiguration, string $table, array $settings, array $tca): void
    {
        if (isset($settings['icon']) && is_string($settings['icon']) && $settings['icon'] !== '') {
            $moduleConfiguration['icon'] = $settings['icon'];

            return;
        }

        if (isset($settings['iconIdentifier']) && is_string($settings['iconIdentifier']) && $settings['iconIdentifier'] !== '') {
            $moduleConfiguration['iconIdentifier'] = $settings['iconIdentifier'];

            return;
        }

        $tableConfig = is_array($tca[$table] ?? null) ? $tca[$table] : [];
        $ctrl = is_array($tableConfig['ctrl'] ?? null) ? $tableConfig['ctrl'] : [];
        $typeIcons = is_array($ctrl['typeicon_classes'] ?? null) ? $ctrl['typeicon_classes'] : [];

        if (isset($ctrl['iconfile']) && is_string($ctrl['iconfile'])) {
            $moduleConfiguration['icon'] = $ctrl['iconfile'];
        } elseif ($typeIcons !== []) {
            $default = $typeIcons['default'] ?? reset($typeIcons);
            $moduleConfiguration['iconIdentifier'] = is_string($default) ? $default : '';
        }
    }
}
