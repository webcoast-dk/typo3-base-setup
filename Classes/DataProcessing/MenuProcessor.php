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
     * Restore the correct shortcut value if it was overwritten by an empty overlay
     *
     * @param $page
     */
    protected function determineOriginalShortcutPage(&$page)
    {
        // Check if modification is required
        if (
            $this->getTypoScriptFrontendController()->sys_language_uid > 0
            && empty($page['shortcut'])
            && !empty($page['uid'])
            && !empty($page['_PAGES_OVERLAY'])
            && !empty($page['_PAGES_OVERLAY_UID'])
        ) {
            // Using raw record since the record was overlaid and is correct already:
            $originalPage = $this->pageRepository->getRawRecord('pages', $page['uid']);

            if ($originalPage['shortcut_mode'] === $page['shortcut_mode'] && !empty($originalPage['shortcut'])) {
                $page['shortcut'] = $originalPage['shortcut'];
            }
        }
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    public function buildConfiguration()
    {
        $this->menuLevelConfig['stdWrap.']['cObject.']['100'] = 'USER';
        $this->menuLevelConfig['stdWrap.']['cObject.']['100.'] = [
            'userFunc' => MenuProcessor::class . '->getFinalLinkParameter',
            'stdWrap.' => [
                'wrap' => ', "finalLinkParameter":|'
            ]
        ];
        parent::buildConfiguration();
    }

    public function getFinalLinkParameter()
    {
        $page = $this->cObj->data;
        if ($page['doktype'] == PageRepository::DOKTYPE_SHORTCUT) {
            $this->determineOriginalShortcutPage($page);
            $shortcutPage = null;
            try {
                $shortcutPage = $this->getTypoScriptFrontendController()->sys_page->getPageShortcut(
                    $page['shortcut'],
                    $page['shortcut_mode'],
                    $page['uid'],
                    20,
                    [],
                    true
                );
            } catch (\Exception $e) {
            }
            $finalLinkParameter = $shortcutPage['uid'];
        } elseif ($page['doktype'] == PageRepository::DOKTYPE_LINK) {
            $url = $this->pageRepository->getExtURL($page);
            $finalLinkParameter = $url;
        } else {
            $finalLinkParameter = $page['uid'];
        }

        return $this->jsonEncode($finalLinkParameter);
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
