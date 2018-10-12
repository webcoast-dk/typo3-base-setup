<?php

// rename media adjustments palette for "image" content type
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$GLOBALS['TCA']['tt_content']['types']['image']['showitem'] = preg_replace(
    '/--palette--;[^;]+?;mediaAdjustments/',
    '--palette--;LLL:EXT:typo3_base_setup/Resources/Private/Language/locallang_backend.xlf:tt_content.palette.crop;mediaAdjustments',
    $GLOBALS['TCA']['tt_content']['types']['image']['showitem']
);

// use "assets" instead of "image" to make it switchable with "textmedia" content type
ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'assets', 'image', 'replace:image');
ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'assets', 'textpic', 'replace:image');

// rename tab "Images" to "Media"
$GLOBALS['TCA']['tt_content']['types']['image']['showitem'] = preg_replace(
    '/(--div--;[^;,]+)tabs.images/',
    '\1tabs.media',
    $GLOBALS['TCA']['tt_content']['types']['image']['showitem']
);
