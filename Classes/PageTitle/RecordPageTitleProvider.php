<?php

namespace WEBcoast\Typo3BaseSetup\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class RecordPageTitleProvider extends AbstractPageTitleProvider
{
    public function getTitle(): string
    {
        $pageTitleFields = GeneralUtility::trimExplode('//', self::getTyposcriptFrontendController()->config['config']['pageTitleFields'] ?? '');
        foreach($pageTitleFields as $field) {
            if (isset(self::getTyposcriptFrontendController()->page[$field]) && !empty(self::getTyposcriptFrontendController()->page[$field])) {
                return self::getTyposcriptFrontendController()->page[$field];
            }
        }

        return '';
    }

    /**
     * @return TypoScriptFrontendController
     */
    private static function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
