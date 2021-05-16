<?php

namespace WEBcoast\Typo3BaseSetup\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Fetch pages for menu structure rendered with FLUIDTEMPLATE
 */
class MenuProcessor extends \TYPO3\CMS\Frontend\DataProcessing\MenuProcessor
{
    /**
     * @var PageRepository
     */
    protected $pageRepository;

    public function __construct()
    {
        parent::__construct();
        $this->pageRepository = $this->getTypoScriptFrontendController()->sys_page;

        $this->allowedConfigurationKeys[] = 'addQueryParams';
    }


    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    public function prepareLevelLanguageConfiguration()
    {
        parent::prepareLevelLanguageConfiguration();

        if (isset($this->processorConfiguration['addQueryParams'])) {
            $addQueryParams = GeneralUtility::trimExplode(',', $this->processorConfiguration['addQueryParams']);
            unset($this->processorConfiguration['addQueryParams']);

            $additionalParams = [];
            foreach ($addQueryParams as $param) {
                $value = GeneralUtility::_GP($param);
                if (!empty($value)) {
                    $additionalParams[$param] = $value;
                }
            }

            if (!empty($additionalParams)) {
                $this->menuLevelConfig['additionalParams'] = '&' . HttpUtility::buildQueryString($additionalParams);
            }
            $this->menuLevelConfig['stdWrap.']['cObject.'] = array_replace_recursive(
                $this->menuLevelConfig['stdWrap.']['cObject.'],
                [
                    '80' => 'TEXT',
                    '80.' => [
                        'value' => json_encode($additionalParams),
                        'wrap' => ',"additionalParams":|'
                    ]
                ]
            );
        }
    }
}
