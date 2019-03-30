<?php

namespace WEBcoast\Typo3BaseSetup\ViewHelpers\Util;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class RecordLanguagesViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('table', 'string', 'The table to check for translated records', true);
        $this->registerArgument('uid', 'int', 'The uid of the original record', true);
        $this->registerArgument('concatenate', 'boolean', 'Concatenate language ids with comma', false, true);
    }

    /**
     * @param array                     $arguments
     * @param \Closure                  $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return array|mixed
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        if (empty($arguments['table'])) {
            throw new \InvalidArgumentException(sprintf('The argument "%s" is empty', 'table'));
        }
        if (empty($arguments['uid'])) {
            throw new \InvalidArgumentException(sprintf('The argument "%s" is empty', 'uid'));
        }

        $tsfe = self::getTyposcriptFrontendController();
        $record = BackendUtility::getRecord($arguments['table'], $arguments['uid']);
        $availableLanguages = [$record[self::getLanguageField($arguments['table'])]];
        $overlayMode = self::getCurrentLanguageAspect()->getOverlayType() === LanguageAspect::OVERLAYS_ON ? 'hideNonTranslated' : '';
        foreach(self::getAvailableLanguages($arguments['table'], $arguments['uid']) as $language) {
            $overlay = $tsfe->sys_page->getRecordOverlay($arguments['table'], $record, $language, $overlayMode);
            if (is_array($overlay)) {
                $availableLanguages[] = $language;
            }
        }

        if ($arguments['concatenate']) {
            return implode(',', $availableLanguages);
        } else {
            return $availableLanguages;
        }
    }

    /**
     * @return TypoScriptFrontendController
     */
    private static function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    private static function getAvailableLanguages($table, $uid)
    {
        if (($languageField = self::getLanguageField($table)) && ($languageParentField = self::getLanguageParentField($table))) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $queryBuilder->select($languageField)->from($table)
                ->where(
                    $queryBuilder->expr()->gt($languageField, 0),
                    $queryBuilder->expr()->eq($languageParentField, $uid)
                );
            $statement = $queryBuilder->execute();
            return $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
        }
        return [];
    }

    private static function getLanguageField($table)
    {
        if ($GLOBALS['TCA'][$table]['ctrl']['languageField']) {
            return $GLOBALS['TCA'][$table]['ctrl']['languageField'];
        }
        return false;
    }

    private static function getLanguageParentField($table)
    {
        if ($GLOBALS['TCA'][$table]['ctrl']['transOrigPointerField']) {
            return $GLOBALS['TCA'][$table]['ctrl']['transOrigPointerField'];
        }
        return false;
    }

    /**
     * @return LanguageAspect
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    private static function getCurrentLanguageAspect()
    {
        return GeneralUtility::makeInstance(Context::class)->getAspect('language');
    }
}
