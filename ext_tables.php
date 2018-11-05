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
// add sub menu setup
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript/SubMenu',
    'Basic page setup (sub menu)'
);

// register backend layout provider
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider']['typo3_base_setup'] = WEBcoast\Typo3BaseSetup\Backend\View\FileBackendLayoutProvider::class;
$extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
if ((bool)$extensionConfiguration['defaultBackendLayoutEnable'] === true) {
    // register default backend layout
    \WEBcoast\Typo3BaseSetup\Utility\ConfigurationUtility::registerBackendLayouts($_EXTKEY);
}

// add default backend typoscript
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:typo3_base_setup/Configuration/TypoScript/Backend/setup.txt">');
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('rte_ckeditor')) {
    // add over default rte preset
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:typo3_base_setup/Configuration/Rte/default.yaml';
}
// add backend rendering hook to show the correct media preview
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] = WEBcoast\Typo3BaseSetup\Hooks\PageLayoutView\MediaPreviewRenderer::class;
// register update wizard
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][WEBcoast\Typo3BaseSetup\Install\FileReferenceWizard::class] = WEBcoast\Typo3BaseSetup\Install\FileReferenceWizard::class;
