<?php

namespace WEBcoast\Typo3BaseSetup\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ExtensionPageTitleProvider extends AbstractPageTitleProvider
{
    public function getTitle(): string
    {
        if (isset(self::getTyposcriptFrontendController()->applicationData['pageTitle']) && !empty(self::getTyposcriptFrontendController()->applicationData['pageTitle'])) {
            return self::getTyposcriptFrontendController()->applicationData['pageTitle'];
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
