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

// register backend layout provider
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider']['typo3_default_setup'] = KappHamburg\Typo3DefaultSetup\Backend\View\FileBackendLayoutProvider::class;
// register default backend layout
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_default_setup']['BackendLayouts'][] = 'typo3_default_setup';

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile($_EXTKEY, 'Configuration/PageTSConfig/page_setup.ts', 'Page setup');
// add over default rte preset
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:typo3_default_setup/Configuration/Rte/default.yaml';
