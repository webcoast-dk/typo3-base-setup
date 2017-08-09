<?php

namespace WEBcoast\Typo3BaseSetup\Hooks\PageLayoutView;


use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MediaPreviewRenderer implements PageLayoutViewDrawItemHookInterface
{

    /**
     * Preprocesses the preview rendering of a content element.
     *
     * @param \TYPO3\CMS\Backend\View\PageLayoutView $parentObject  Calling parent object
     * @param bool                                   $drawItem      Whether to draw the item using the default functionalities
     * @param string                                 $headerContent Header content
     * @param string                                 $itemContent   Item content
     * @param array                                  $row           Record row of tt_content
     */
    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row)
    {
//        if ($drawItem === true) {
            $CType = $row['CType'];
            $configuration = $GLOBALS['TCA']['tt_content']['types'][$CType];
            $fieldsShown = [];
            $content = '';
            foreach (GeneralUtility::trimExplode(',', $configuration['showitem'], true) as $item) {
                list($name, $label, $palette) = GeneralUtility::trimExplode(';', $item);
                if (strpos($name, '--') === false) {
                    $fieldsShown[] = $name;
                } elseif ($name === '--palette--' && !empty($palette)) {
                    $paletteConfiguration = $GLOBALS['TCA']['tt_content']['palettes'][$palette];
                    foreach (GeneralUtility::trimExplode(
                        ',',
                        $paletteConfiguration['showitem'],
                        true
                    ) as $paletteItem) {
                        list($name, $label) = GeneralUtility::trimExplode(';', $paletteItem);
                        $fieldsShown[] = $name;
                    }
                }
            }
            foreach ($fieldsShown as $field) {
                if ($field !== 'bodytext') {
                    $fieldConfiguration = $GLOBALS['TCA']['tt_content']['columns'][$field]['config'];
                    if ($fieldConfiguration['type'] === 'inline' && $fieldConfiguration['foreign_table'] === 'sys_file_reference' && $row[$field]) {
                        $content = $parentObject->linkEditContent(
                                $parentObject->getThumbCodeUnlinked($row, 'tt_content', $field),
                                $row
                            ) . '<br />';
                    }
                }
            }
            if ($content !== '') {
                if (in_array('bodytext', $fieldsShown) && $row['bodytext']) {
                    $itemContent .= $parentObject->linkEditContent(
                            $parentObject->renderText($row['bodytext']),
                            $row
                        ) . '<br />';
                }
                $itemContent .= $content;
                $drawItem = false;
            }
//        }
    }
}