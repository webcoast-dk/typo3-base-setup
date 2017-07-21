<?php

namespace WEBcoast\Typo3DefaultSetup\DataProcessing;


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
                $shortcutPage = $this->getTypoScriptFrontendController()->getPageShortcut(
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
}