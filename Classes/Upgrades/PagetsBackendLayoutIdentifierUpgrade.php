<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Upgrades;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Upgrades\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;

/**
 * Migrates pages.backend_layout from "default" (core, colPos 0 only)
 * to "pagets__default" (Page TSconfig layout with colPos 0–6).
 */
#[UpgradeWizard('maiThemePagetsBackendLayoutIdentifierUpgrade')]
final readonly class PagetsBackendLayoutIdentifierUpgrade implements UpgradeWizardInterface
{
    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    public function getTitle(): string
    {
        return 'Migrate page backend layouts to pagets__default identifier';
    }

    public function getDescription(): string
    {
        return sprintf(
            'Updates %d page records from backend_layout "default" to "pagets__default" so Visual Editor content areas colPos 0–6 resolve correctly.',
            $this->getCountOfRowsWhichNeedUpdate(),
        );
    }

    public function updateNecessary(): bool
    {
        return $this->getCountOfRowsWhichNeedUpdate() > 0;
    }

    /**
     * @return list<class-string>
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    public function executeUpdate(): bool
    {
        $connection = $this->connectionPool->getConnectionForTable('pages');
        $connection->update(
            'pages',
            ['backend_layout' => 'pagets__default'],
            ['backend_layout' => 'default'],
            ['backend_layout' => Connection::PARAM_STR],
        );

        return true;
    }

    private function getCountOfRowsWhichNeedUpdate(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $queryBuilder->getRestrictions()->removeAll();

        return (int) $queryBuilder
            ->count('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq(
                    'backend_layout',
                    $queryBuilder->createNamedParameter('default', Connection::PARAM_STR),
                ),
            )
            ->executeQuery()
            ->fetchOne();
    }
}
