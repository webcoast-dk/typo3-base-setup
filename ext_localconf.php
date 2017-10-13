<?php


if (!defined('TYPO3_MODE')) {
    die('Access denied!');
}
$extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
if ((bool)$extensionConfiguration['enableEmbedYouTubeHelper'] === true) {
    // register embed youtube helper
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['youtube'] = WEBcoast\Typo3BaseSetup\Resources\OnlineMedia\Helpers\EmbedYouTubeHelper::class;
}
