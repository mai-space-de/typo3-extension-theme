<?php

declare(strict_types=1);

use Maispace\Theme\Services\RecordModuleRegistrar;
use TYPO3\CMS\Core\Utility\GeneralUtility;

return GeneralUtility::makeInstance(RecordModuleRegistrar::class)->buildModuleRegistrations();
