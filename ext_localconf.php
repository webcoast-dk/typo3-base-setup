<?php


if (!defined('TYPO3_MODE')) {
    die('Access denied!');
}
$extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
if ((bool)$extensionConfiguration['enableEmbedYouTubeHelper'] === true) {
    // register embed youtube helper
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['onlineMediaHelpers']['youtube'] = WEBcoast\Typo3BaseSetup\Resources\OnlineMedia\Helpers\EmbedYouTubeHelper::class;
}

/** @var \TYPO3\CMS\Core\Configuration\ExtensionConfiguration $extensionConfiguration */
$extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class);
if ($extensionConfiguration->get('typo3_base_setup', 'enablePictureTagRenderer')) {
    $rendererRegistry = \TYPO3\CMS\Core\Resource\Rendering\RendererRegistry::getInstance();
    $rendererRegistry->registerRendererClass(\WEBcoast\Typo3BaseSetup\Resources\Rendering\PictureTagRenderer::class);
}
