<?php

use Mogic\ChangelogInfo\Controller\ChangelogInfoController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

//TYPO3v11-way of registering backend module at third level
ExtensionManagementUtility::insertModuleFunction(
    'web_info',
    ChangelogInfoController::class,
    '',
    'LLL:EXT:changelog_info/Resources/Private/Language/locallang.xlf:backend.showChangelog'
);
