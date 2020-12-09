<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied!');
}

// register backend layout provider
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutDataProvider']['typo3_base_setup'] = WEBcoast\Typo3BaseSetup\Backend\View\FileBackendLayoutProvider::class;
$extensionConfiguration = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class);
if ((bool)(int)$extensionConfiguration->get('typo3_base_setup', 'defaultBackendLayoutEnable') === true) {
    // register default backend layout
    WEBcoast\Typo3BaseSetup\Utility\ConfigurationUtility::registerBackendLayouts('typo3_base_setup');
}

// add default backend typoscript
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:typo3_base_setup/Configuration/TypoScript/Backend/setup.txt">');
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('rte_ckeditor')) {
    // add over default rte preset
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:typo3_base_setup/Configuration/Rte/default.yaml';
}
// register update wizard
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][WEBcoast\Typo3BaseSetup\Install\FileReferenceWizard::class] = WEBcoast\Typo3BaseSetup\Install\FileReferenceWizard::class;
