<?php

namespace WEBcoast\Typo3BaseSetup\Backend\View;

use TYPO3\CMS\Backend\View\BackendLayout;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class FileBackendLayoutProvider implements BackendLayout\DataProviderInterface
{

    /**
     * Adds backend layouts to the given backend layout collection.
     *
     * @param BackendLayout\DataProviderContext     $dataProviderContext
     * @param BackendLayout\BackendLayoutCollection $backendLayoutCollection
     *
     * @return void
     */
    public function addBackendLayouts(BackendLayout\DataProviderContext $dataProviderContext, BackendLayout\BackendLayoutCollection $backendLayoutCollection)
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_base_setup']['BackendLayouts'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_base_setup']['BackendLayouts'] as $extensionKey => $configuration) {
                list ($path, $languageFile) = $configuration;
                // set default path, if no path is set
                if (empty($path)) {
                    $path = '/Configuration/BackendLayouts/';
                }
                // make sure $path start with a slash
                if (substr($path, 0, 1) !== '/') {
                    $path = '/' . $path;
                }
                // get absolute file path
                $directory = GeneralUtility::getFileAbsFileName(
                    'EXT:' . $extensionKey . $path
                );
                $fileNames = scandir($directory);
                if (is_array($fileNames)) {
                    foreach ($fileNames as $fileName) {
                        if (substr($fileName, 0, 1) === '.') {
                            continue;
                        }

                        if (fnmatch('*.typoscript', $fileName)) {
                            $identifier = $extensionKey . '-' . substr($fileName, 0, -11);
                            $backendLayout = $this->getBackendLayout($identifier, $dataProviderContext->getPageId());
                            if ($backendLayout !== null) {
                                $backendLayoutCollection->add($backendLayout);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets a backend layout by (regular) identifier.
     *
     * @param string  $identifier
     * @param integer $pageId
     *
     * @return NULL|BackendLayout\BackendLayout
     */
    public function getBackendLayout($identifier, $pageId)
    {
        list ($extensionKey, $identifier) = GeneralUtility::trimExplode('-', $identifier);
        $configuration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_base_setup']['BackendLayouts'][$extensionKey];
        list ($path, $languageFile) = $configuration;
        // set default path, if no path is set
        if (empty($path)) {
            $path = '/Configuration/BackendLayouts/';
        } elseif (substr($path, 0, 1) !== '/') {
            // make sure $path starts with a slash
            $path = '/' . $path;
        }
        // set default language file, if it is not set
        if (empty($languageFile)) {
            $languageFile = '/Resources/Private/Language/locallang_backend.xlf';
        } elseif (substr($languageFile, 0, 1) !== '/') {
            // make sure $languageFile starts with a slash
            $languageFile = '/' . $languageFile;
        }
        // get absolute file path
        $directory = GeneralUtility::getFileAbsFileName('EXT:' . $extensionKey . $path);
        $fileName = $identifier . '.typoscript';
        if (is_file($directory . '/' . $fileName) && fnmatch('*.typoscript', $fileName)) {
            // translate be_layout
            $locallangId = 'LLL:EXT:' . $extensionKey . $languageFile . ':backendLayouts.' . $identifier;
            $title = LocalizationUtility::translate($locallangId, $extensionKey);
            if ($title === null) {
                $title = $identifier;
            }
            $backendLayout = new BackendLayout\BackendLayout(
                $extensionKey . '-' . $identifier,
                $title,
                file_get_contents($directory . '/' . $fileName)
            );

            return $backendLayout;
        }

        return null;
    }
}
