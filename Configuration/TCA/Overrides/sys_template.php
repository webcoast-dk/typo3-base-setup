<?php

// add default setup
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Base',
    'Basic page setup (main)'
);
// add language menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/LanguageMenu',
    'Basic page setup (language menu)'
);
// add breadcrumb menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/BreadcrumbMenu',
    'Basic page setup (breadcrumb menu)'
);
// add header menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/HeaderMenu',
    'Basic page setup (header menu)'
);
// add footer menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/FooterMenu',
    'Basic page setup (footer menu)'
);
// add sub menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/SubMenu',
    'Basic page setup (sub menu)'
);
// add page title provider setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/PageTitleProvider',
    'Basic page setup (page title provider)'
);
// add gallery data processor setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/GalleryDataProcessor',
    'Basic page setup (gallery data processor)'
);
