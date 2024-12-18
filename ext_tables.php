<?php

use Mogic\ChangelogInfo\Controller\ChangelogInfoController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Information\Typo3Version;

defined('TYPO3') or die();



//TYPO3v11-way of registering backend module at third level
$version = new Typo3Version();
if ($version->getMajorVersion() < 12) {
    ExtensionManagementUtility::insertModuleFunction(
        'web_info',
        ChangelogInfoController::class,
        '',
        'LLL:EXT:changelog_info/Resources/Private/Language/locallang.xlf:backend.showChangelog'
    );
}
