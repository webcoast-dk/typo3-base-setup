<?php

// add default setup
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Base',
    'Basic page setup (main)'
);
// add main menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Menu/Main',
    'Basic page setup (main menu)'
);
// add language menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Menu/Language',
    'Basic page setup (language menu)'
);
// add breadcrumb menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Menu/Breadcrumb',
    'Basic page setup (breadcrumb menu)'
);
// add header menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Menu/Header',
    'Basic page setup (header menu)'
);
// add footer menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Menu/Footer',
    'Basic page setup (footer menu)'
);
// add sub menu setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Menu/Sub',
    'Basic page setup (sub menu)'
);
// add site data setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Data/Site',
    'Basic page setup (site data)'
);
// add homepage data setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/Data/Homepage',
    'Basic page setup (homepage data)'
);
// add page title provider setup
ExtensionManagementUtility::addStaticFile(
    'typo3_base_setup',
    'Configuration/TypoScript/PageTitleProvider',
    'Basic page setup (page title provider)'
);
