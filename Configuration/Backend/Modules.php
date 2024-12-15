<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use T3docs\Examples\Controller\AdminModuleController;
use T3docs\Examples\Controller\ModuleController;

/**
 * Definitions for modules provided by EXT:examples
 */
return [
    'changelog_info' => [
        'parent' => 'web',
        'position' => ['after' => 'web_info'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/system/example',
        'labels' => 'LLL:EXT:changelog_info/Resources/Private/Language/locallang.xlf:backend.showChangelog',
        'iconIdentifier' => 'changelog_info',
        'routes' => [
            '_default' => [
                'target' => \Mogic\ChangelogInfo\Controller\ChangelogInfoController::class . '::main',
            ],
        ],
    ],
];