<?php

namespace WEBcoast\Typo3BaseSetup\DataProcessing;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class FlexFormProcessor implements DataProcessorInterface
{

    /**
     * Process content object data
     *
     * @param ContentObjectRenderer $cObj                       The data of the content element or page
     * @param array                 $contentObjectConfiguration The configuration of Content Object
     * @param array                 $processorConfiguration     The configuration of this processor
     * @param array                 $processedData              Key/value store of processed data (e.g. to be passed to
     *                                                          a Fluid View)
     *
     * @return array the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    )
    {
        $flexFormField = $cObj->stdWrapValue('fieldName', $processorConfiguration, 'pi_flexform');
        $as = $cObj->stdWrapValue('as', $processorConfiguration, 'flexFormData');
        $path = $cObj->stdWrapValue('path', $processorConfiguration, '');

        if (isset($cObj->data[$flexFormField])) {
            /** @var FlexFormService $flexFormService */
            $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
            $flexFormData = $flexFormService->convertFlexFormContentToArray($cObj->data[$flexFormField]);

            if (!empty($path)) {
                $pathArray = GeneralUtility::trimExplode('.', $path);
                foreach($pathArray as $currentPath) {
                    if (isset($flexFormData[$currentPath])) {
                        $flexFormData = $flexFormData[$currentPath];
                    } else {
                        $flexFormData = null;
                    }
                }
            }
        } else {
            $flexFormData = null;
        }
        $processedData[$as] = $flexFormData;

        return $processedData;
    }
}