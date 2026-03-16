<?php

declare(strict_types=1);

namespace Maispace\Theme\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Controller\RecordListController;
use TYPO3\CMS\Backend\Module\ModuleInterface;
use TYPO3\CMS\Backend\RecordList\DatabaseRecordList;
use TYPO3\CMS\Backend\Routing\Route;
use TYPO3\CMS\Backend\Template\Components\Buttons\GenericButton;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsController]
class RecordModuleController extends RecordListController
{
    public function mainAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->moduleData = $request->getAttribute('moduleData');

        /** @var Route|null $route */
        $route = $request->getAttribute('route'); // @phpstan-ignore phpstanTypo3.requestAttributeValidation
        $module = $route instanceof Route ? $route->getOption('module') : null;

        if ($module instanceof ModuleInterface && $this->moduleData !== null) {
            $defaultData = $module->getDefaultModuleData();
            $this->moduleData->set('table', $defaultData['table'] ?? '');
            $this->moduleData->set('pids', $defaultData['pids'] ?? []);
        }

        $backendUser = $this->getBackendUserAuthentication();
        /** @var array<string, mixed> $parsedBody */
        $parsedBody = is_array($request->getParsedBody()) ? $request->getParsedBody() : [];
        /** @var array<string, mixed> $queryParams */
        $queryParams = $request->getQueryParams();

        $permsClause = $backendUser->getPagePermsClause(Permission::PAGE_SHOW);
        $currentTable = is_string($this->moduleData?->get('table')) ? (string)$this->moduleData->get('table') : '';

        $rawId = $parsedBody['id'] ?? $queryParams['id'] ?? 0;
        $this->id = is_numeric($rawId) ? (int)$rawId : 0;
        $this->table = $currentTable;

        $pids = $this->resolvePids($parsedBody, $queryParams);

        $pids = $this->filterAccessiblePids($pids, $permsClause);

        $this->pageRenderer->addInlineLanguageLabelFile(
            'EXT:core/Resources/Private/Language/locallang_mod_web_list.xlf'
        );

        BackendUtility::lockRecords();

        if ($this->id === 0 && $pids !== []) {
            $this->id = (int)reset($pids);
        }

        $rawPointer = $parsedBody['pointer'] ?? $queryParams['pointer'] ?? 0;
        $pointer = max(0, is_numeric($rawPointer) ? (int)$rawPointer : 0);
        $rawSearchTerm = $parsedBody['searchTerm'] ?? $queryParams['searchTerm'] ?? '';
        $this->searchTerm = trim(is_string($rawSearchTerm) ? $rawSearchTerm : '');
        $searchLevels = 0;
        $rawReturnUrl = $parsedBody['returnUrl'] ?? $queryParams['returnUrl'] ?? '';
        $this->returnUrl = GeneralUtility::sanitizeLocalUrl(
            is_string($rawReturnUrl) ? $rawReturnUrl : ''
        );

        $site = $request->getAttribute('site');
        $siteLanguages = $site !== null ? $site->getAvailableLanguages(
            $backendUser,
            false,
            $this->id
        ) : [];

        $pageInfo = BackendUtility::readPageAccess($this->id, $permsClause);
        $access = is_array($pageInfo);
        $this->pageInfo = is_array($pageInfo) ? $pageInfo : [];
        $this->pagePermissions = new Permission($backendUser->calcPerms($this->pageInfo));

        $view = $this->moduleTemplateFactory->create($request);

        if ($pids === []) {
            $view->addFlashMessage(
                $this->getLanguageService()->sL(
                    'LLL:EXT:theme/Resources/Private/Language/locallang_record_module.xlf:noPagesForThisTable'
                ),
                '',
                ContextualFeedbackSeverity::ERROR
            );
            return $view->renderResponse('RecordModule/List');
        }

        if (!$backendUser->isAdmin() && !$backendUser->check('tables_select', $currentTable)) {
            $view->addFlashMessage(
                $this->getLanguageService()->sL(
                    'LLL:EXT:theme/Resources/Private/Language/locallang_record_module.xlf:noAccess'
                ),
                '',
                ContextualFeedbackSeverity::ERROR
            );
            return $view->renderResponse('RecordModule/List');
        }

        $this->allowSearch = true;

        if ($this->searchTerm !== '' && $this->moduleData !== null) {
            $this->moduleData->set('searchBox', true);
        }

        $dbList = GeneralUtility::makeInstance(DatabaseRecordList::class);
        $dbList->setRequest($request);
        if ($this->moduleData !== null) {
            $dbList->setModuleData($this->moduleData);
        }
        $dbList->calcPerms = $this->pagePermissions;
        $dbList->returnUrl = $this->returnUrl;
        $dbList->showClipboardActions = true;
        $dbList->disableSingleTableView = false;
        $dbList->listOnlyInSingleTableMode = false;

        /** @var array<string, mixed> $tca */
        $tca = (array)($GLOBALS['TCA'] ?? []);
        $tablesToHide = array_keys($tca);
        $tableIndex = array_search($currentTable, $tablesToHide, true);
        if ($tableIndex !== false) {
            unset($tablesToHide[$tableIndex]);
        }
        $dbList->hideTables = implode(',', $tablesToHide);
        $dbList->allowedNewTables = [$currentTable];

        $dbList->hideTranslations = '';
        $dbList->tableTSconfigOverTCA = [];
        /** @var array<string> $pageRow */
        $pageRow = $this->pageInfo;
        $dbList->pageRow = $pageRow;
        /** @var array<array<mixed>> $modTSconfig */
        $modTSconfig = $this->modTSconfig;
        $dbList->modTSconfig = $modTSconfig;
        $dbList->setLanguagesAllowedForUser($siteLanguages);

        $clipBoardEnabled = $this->moduleData !== null && (bool)$this->moduleData->get('clipBoard');
        $clipboard = $this->initializeClipboard($request, $clipBoardEnabled);
        $dbList->clipObj = $clipboard;

        $rawCmd = $parsedBody['cmd'] ?? $queryParams['cmd'] ?? '';
        $cmd = is_string($rawCmd) ? $rawCmd : '';

        $tableListHtml = '';
        if ($access || ($this->id === 0 && $this->searchTerm !== '')) {
            if ($cmd === 'delete' && $request->getMethod() === 'POST') {
                $this->deleteRecords($request, $clipboard);
            }
            $dbList->start($this->id, $this->table, $pointer, $this->searchTerm, $searchLevels);
            $tableListHtml = $dbList->generateList();
        }

        $searchBoxHtml = '';
        $searchBoxEnabled = $this->moduleData !== null && (bool)$this->moduleData->get('searchBox');
        if ($this->allowSearch && $searchBoxEnabled
            && ($tableListHtml !== '' || $this->searchTerm !== '')) {
            $searchBoxHtml = $this->renderSearchBox($request, $dbList, $this->searchTerm, $searchLevels);
        }

        $clipboardHtml = '';
        if ($clipBoardEnabled && ($tableListHtml !== '' || $clipboard->hasElements())) {
            $clipboardHtml = '<typo3-backend-clipboard-panel return-url="'
                . htmlspecialchars($dbList->listURL())
                . '"></typo3-backend-clipboard-panel>';
        }

        if ($tableListHtml === '') {
            $this->addNoRecordsFlashMessage($view, $this->table);
            $newRecordButton = $this->createNewRecordButton($dbList->listURL());
            $tableListHtml = (string)$newRecordButton;
        }

        if (is_array($pageInfo)) {
            $view->getDocHeaderComponent()->setMetaInformation($pageInfo);
        }

        $this->getDocHeaderButtons($view, $clipboard, $request, $dbList);

        $moduleTitle = $this->resolveModuleTitle();

        $view->assignMultiple([
            'pageId' => $this->id,
            'table' => $this->table,
            'moduleTitle' => $moduleTitle,
            'searchBoxHtml' => $searchBoxHtml,
            'tableListHtml' => $tableListHtml,
            'clipboardHtml' => $clipboardHtml,
        ]);

        return $view->renderResponse('RecordModule/List');
    }

    private function resolveModuleTitle(): string
    {
        $title = $this->moduleData !== null ? $this->moduleData->get('title') : null;
        if (is_string($title) && $title !== '') {
            if (str_starts_with($title, 'LLL:')) {
                return $this->getLanguageService()->sL($title);
            }
            return $title;
        }

        /** @var array<string, mixed> $tca */
        $tca = (array)($GLOBALS['TCA'] ?? []);
        $tableConfig = is_array($tca[$this->table] ?? null) ? $tca[$this->table] : [];
        $ctrl = is_array($tableConfig['ctrl'] ?? null) ? $tableConfig['ctrl'] : [];
        $tcaTitle = is_string($ctrl['title'] ?? null) ? $ctrl['title'] : $this->table;

        if (str_starts_with($tcaTitle, 'LLL:')) {
            return $this->getLanguageService()->sL($tcaTitle);
        }

        return $tcaTitle;
    }

    /**
     * @param array<string, mixed> $parsedBody
     * @param array<string, mixed> $queryParams
     * @return list<int>
     */
    private function resolvePids(array $parsedBody, array $queryParams): array
    {
        $configuredPids = $this->moduleData?->get('pids');

        if (is_array($configuredPids) && $configuredPids !== []) {
            return array_values(array_map(static fn(mixed $v): int => is_numeric($v) ? (int)$v : 0, $configuredPids));
        }

        if (is_string($configuredPids) && trim($configuredPids) !== '') {
            return GeneralUtility::intExplode(',', $configuredPids, true);
        }

        $rawId = $parsedBody['id'] ?? $queryParams['id'] ?? 0;
        $id = is_numeric($rawId) ? (int)$rawId : 0;
        if ($id > 0) {
            return [$id];
        }

        return [];
    }

    /**
     * @param list<int> $pids
     * @return list<int>
     */
    private function filterAccessiblePids(array $pids, string $permsClause): array
    {
        return array_values(array_filter($pids, static function (int $pid) use ($permsClause): bool {
            $pageInfo = BackendUtility::readPageAccess($pid, $permsClause);
            return is_array($pageInfo);
        }));
    }

    private function createNewRecordButton(string $returnUrl): GenericButton
    {
        $label = $this->getLanguageService()->sL(
            'LLL:EXT:core/Resources/Private/Language/locallang_mod_web_list.xlf:new'
        );

        $href = (string)$this->uriBuilder->buildUriFromRoute(
            'record_edit',
            [
                'edit' => [
                    $this->table => [
                        $this->id => 'new',
                    ],
                ],
                'returnUrl' => $returnUrl,
            ]
        );

        $button = GeneralUtility::makeInstance(GenericButton::class);
        $button->setTag('a');
        $button->setLabel($label);
        $button->setShowLabelText(true);
        $button->setIcon($this->iconFactory->getIcon('actions-plus', IconSize::SMALL));
        $button->setAttributes(['href' => $href, 'data-recordlist-action' => 'new']);

        return $button;
    }
}
