<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full license information please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Boilerplate for a functional test phpunit bootstrap file.
 *
 * Defines ORIGINAL_ROOT so that FunctionalTestCase can locate
 * the project root and set up a test instance.
 *
 * @see \TYPO3\TestingFramework\Core\Functional\FunctionalTestCase
 */
(static function () {
    // Explicitly set ORIGINAL_ROOT to the project root (4 levels up from
    // Tests/Functional/) because the extension has its own vendor/ directory
    // that would otherwise mislead defineOriginalRootPath().
    if (!defined('ORIGINAL_ROOT')) {
        define('ORIGINAL_ROOT', dirname(__DIR__, 4) . '/');
    }

    $testbase = new \TYPO3\TestingFramework\Core\Testbase();
    $testbase->createDirectory(ORIGINAL_ROOT . 'typo3temp/var/tests');
    $testbase->createDirectory(ORIGINAL_ROOT . 'typo3temp/var/transient');
})();
