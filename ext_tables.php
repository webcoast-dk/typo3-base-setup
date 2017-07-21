<?php


if (!defined('TYPO3_MODE')) {
    die('Access denied!');
}

// add default setup
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/Base',
    'Basic page setup (main)'
);
// add language menu setup
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/LanguageMenu',
    'Basic page setup (language menu)'
);
// add breadcrumb menu setup
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/BreadcrumbMenu',
    'Basic page setup (breadcrumb menu)'
);
// add header menu setup
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/HeaderMenu',
    'Basic page setup (header menu)'
);
// add footer menu setup
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/FooterMenu',
    'Basic page setup (footer menu)'
);

// register backend layout provider
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider']['typo3_base_setup'] = WEBcoast\Typo3BaseSetup\Backend\View\FileBackendLayoutProvider::class;
// register default backend layout
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_base_setup']['BackendLayouts'][] = 'typo3_base_setup';

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile($_EXTKEY, 'Configuration/PageTSConfig/page_setup.ts', 'Page setup');
// add default backend typoscript
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:typo3_base_setup/Configuration/TypoScript/Backend/setup.txt">');
// add over default rte preset
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:typo3_base_setup/Configuration/Rte/default.yaml';
