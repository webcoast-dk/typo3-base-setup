<?php

namespace WEBcoast\Typo3BaseSetup\DataProcessing;

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class EnrichLanguageMenuProcessor implements DataProcessorInterface
{
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        $as = $processorConfiguration['as'] ?? 'configuration';

        $site = $this->getCurrentSite();
        $siteLanguage = $site->getLanguageById($processedData['languageUid']);
        $processedData[$as] = $siteLanguage->toArray();

        return $processedData;
    }

    /**
     * Returns the currently configured "site" if a site is configured (= resolved) in the current request.
     *
     * @return SiteInterface|null
     */
    protected function getCurrentSite(): ?SiteInterface
    {
        try {
            return $this->getSiteFinder()->getSiteByPageId($this->getCurrentPageId());
        } catch (SiteNotFoundException $e) {
            // Do nothing
        }

        return null;
    }

    /**
     * @return SiteFinder
     */
    protected function getSiteFinder(): SiteFinder
    {
        return GeneralUtility::makeInstance(SiteFinder::class);
    }

    /**
     * @return int
     */
    protected function getCurrentPageId(): int
    {
        return (int)$GLOBALS['TSFE']->id;
    }
}
