<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

(function () {
    $dynamicHeaderMenu = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('typo3_base_setup', 'dynamicHeaderMenu');
    $dynamicFooterMenu = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('typo3_base_setup', 'dynamicFooterMenu');

    if ($dynamicHeaderMenu || $dynamicFooterMenu) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', '--div--;LLL:EXT:typo3_base_setup/Resources/Private/Language/locallang_backend.xlf:pages.tabs.menu');

        if ($dynamicHeaderMenu) {
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', [
                'header_menu' => [
                    'label' => 'LLL:EXT:typo3_base_setup/Resources/Private/Language/locallang_backend.xlf:pages.header_menu.label',
                    'config' => [
                        'type' => 'group',
                        'internal_type' => 'db',
                        'allowed' => 'pages',
                        'MM' => 'tx_typo3basesetup_menu',
                        'MM_match_fields' => [
                            'field_name' => 'header_menu'
                        ],
                        'minitems' => 0,
                        'autoSizeMax' => 20,
                    ],
                    'displayCond' => 'FIELD:is_siteroot:=:1',
                ]
            ]);
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', 'header_menu');
        }

        if ($dynamicFooterMenu) {
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', [
                'footer_menu' => [
                    'label' => 'LLL:EXT:typo3_base_setup/Resources/Private/Language/locallang_backend.xlf:pages.footer_menu.label',
                    'config' => [
                        'type' => 'group',
                        'internal_type' => 'db',
                        'allowed' => 'pages',
                        'MM' => 'tx_typo3basesetup_menu',
                        'MM_match_fields' => [
                            'field_name' => 'footer_menu'
                        ],
                        'minitems' => 0,
                        'autoSizeMax' => 20,
                    ],
                    'displayCond' => 'FIELD:is_siteroot:=:1',
                ]
            ]);
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', 'footer_menu');
        }
    }
})();
