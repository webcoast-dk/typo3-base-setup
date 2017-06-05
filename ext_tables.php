<?php


if (!defined('TYPO3_MODE')) {
    die('Access denied!');
}

// add default setup
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/Base',
    'Default page setup (Base)'
);
// add language menu setup
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/LanguageMenu',
    'Default page setup (Language menu)'
);
