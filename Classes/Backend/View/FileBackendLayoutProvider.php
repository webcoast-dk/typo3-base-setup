<?php

namespace WEBcoast\Typo3DefaultSetup\Backend\View;

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
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_default_setup']['BackendLayouts'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_default_setup']['BackendLayouts'] as $extensionKey) {
                $directory = GeneralUtility::getFileAbsFileName(
                    'EXT:' . $extensionKey . '/Configuration/BackendLayouts/'
                );
                $fileNames = scandir($directory);
                if (is_array($fileNames)) {
                    foreach ($fileNames as $fileName) {
                        if (substr($fileName, 0, 1) === '.') {
                            continue;
                        }

                        if (fnmatch('*.ts', $fileName)) {
                            $identifier = $extensionKey . '-' . substr($fileName, 0, -3);
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
        $directory = GeneralUtility::getFileAbsFileName('EXT:' . $extensionKey . '/Configuration/BackendLayouts/');
        $fileName = $identifier . '.ts';
        if (is_file($directory . '/' . $fileName) && fnmatch('*.ts', $fileName)) {
            // translate be_layout
            $locallangId = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_backend.xlf:backendLayouts.' . strtolower(
                    $identifier
                );
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
