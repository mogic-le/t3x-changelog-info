<?php

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
