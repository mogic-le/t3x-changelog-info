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

return [
    'web_info_changelog_info' => [
        'parent' => 'web_info',
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/changelog-info',
        'labels' => [
            'title' => 'LLL:EXT:changelog_info/Resources/Private/Language/locallang.xlf:backend.showChangelog'
        ],
        'routes' => [
            '_default' => [
                'target' => \Mogic\ChangelogInfo\Controller\ChangelogInfoController::class . '::main',
            ],
        ],
    ],
];
