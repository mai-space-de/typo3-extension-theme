<?php

declare(strict_types=1);

namespace Maispace\MaiTheme\Command;

use Doctrine\DBAL\ParameterType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Move main-navigation pages under Hauptnavigation (uid=28).
 *
 * Implements the human decision (Option B) to organise pages as children of
 * the Hauptnavigation sys-folder so that the MenuProcessor
 * (`special = directory` → page 28) can discover them.
 */
#[AsCommand(
    name: 'theme:move-navigation-pages',
    description: 'Move main-navigation pages under the Hauptnavigation folder (uid=28).',
)]
final class MoveNavigationPagesCommand extends Command
{
    private const TARGET_PID = 28;

    /** @var int[] Default-language page UIDs belonging in the main nav */
    private const MAIN_NAV_PAGE_UIDS = [
        2, 6, 10, 11, 12, 13, 16, 17, 18, 19, 23, 24,
    ];

    protected function configure(): void
    {
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Preview which pages would be moved without actually executing.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');

        $io->title('Move Navigation Pages');
        $io->writeln(sprintf('Target folder: page uid %d (Hauptnavigation)', self::TARGET_PID));

        $allUids = $this->resolveAllPageUids();
        $this->printPageList($io, self::MAIN_NAV_PAGE_UIDS);

        if ($dryRun) {
            $io->note('Dry-run mode — no changes were made.');
            return Command::SUCCESS;
        }

        $io->section('Executing move…');

        $connection = $this->getConnection();

        $moved = 0;
        $total = count($allUids);
        $io->progressStart($total);

        foreach ($allUids as $uid) {
            $affected = $connection->update(
                'pages',
                ['pid' => self::TARGET_PID],
                ['uid' => (int) $uid],
                ['pid' => Connection::PARAM_INT, 'uid' => Connection::PARAM_INT],
            );
            if ($affected > 0) {
                $moved++;
            }
            $io->progressAdvance();
        }

        $io->progressFinish();

        if ($moved === 0) {
            $io->warning('No pages were moved. Verify the page UIDs exist and are not deleted.');
            return Command::FAILURE;
        }

        $io->success(sprintf(
            'Moved %d/%d pages under Hauptnavigation (uid=%d).',
            $moved,
            $total,
            self::TARGET_PID,
        ));

        $io->note('Run `ddev typo3 cache:flush` to ensure the frontend picks up the changes.');

        return Command::SUCCESS;
    }

    /** @return int[] */
    private function resolveAllPageUids(): array
    {
        $uids = self::MAIN_NAV_PAGE_UIDS;
        foreach (self::MAIN_NAV_PAGE_UIDS as $defaultUid) {
            foreach ($this->getTranslationInfo($defaultUid) as $t) {
                $uids[] = (int) $t['uid'];
            }
        }
        return $uids;
    }

    private function printPageList(SymfonyStyle $io, array $pageUids): void
    {
        $io->section('Pages to move');
        foreach ($pageUids as $uid) {
            $info = $this->getPageInfo($uid);
            $l10nInfo = $this->getTranslationInfo($uid);
            $msg = sprintf('  - [%d] %s', $uid, $info['title']);
            if ($l10nInfo !== []) {
                $labels = array_map(
                    fn(array $t): string => sprintf(
                        '%d (%s)',
                        $t['uid'],
                        $t['sys_language_uid'] === 1 ? 'en' : ($t['sys_language_uid'] === 2 ? 'uk' : 'ar'),
                    ),
                    $l10nInfo,
                );
                $msg .= ' → translations: ' . implode(', ', $labels);
            }
            $io->writeln($msg);
        }
    }

    private function getConnection(): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('pages');
    }

    /** @return array{uid: int, title: string, pid: int} */
    private function getPageInfo(int $uid): array
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $row = $qb
            ->select('uid', 'title', 'pid')
            ->from('pages')
            ->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid, ParameterType::INTEGER)))
            ->executeQuery()
            ->fetchAssociative();
        return $row !== false ? $row : ['uid' => $uid, 'title' => 'NOT FOUND', 'pid' => -1];
    }

    /** @return array<int, array{uid: int, sys_language_uid: int}> */
    private function getTranslationInfo(int $defaultUid): array
    {
        $qb = $this->getConnection()->createQueryBuilder();
        return $qb
            ->select('uid', 'sys_language_uid')
            ->from('pages')
            ->where(
                $qb->expr()->eq('l10n_parent', $qb->createNamedParameter($defaultUid, ParameterType::INTEGER)),
                $qb->expr()->gt('sys_language_uid', $qb->createNamedParameter(0, ParameterType::INTEGER)),
                $qb->expr()->eq('deleted', $qb->createNamedParameter(0, ParameterType::INTEGER)),
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
