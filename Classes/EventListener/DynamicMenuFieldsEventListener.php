<?php

declare(strict_types=1);

namespace WEBcoast\Typo3BaseSetup\EventListener;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DynamicMenuFieldsEventListener
{
    public function __invoke(AlterTableDefinitionStatementsEvent $event): void
    {
        $sqlData = $event->getSqlData();

        $dynamicHeaderMenu = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('typo3_base_setup', 'dynamicHeaderMenu');
        $dynamicFooterMenu = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('typo3_base_setup', 'dynamicFooterMenu');

        if ($dynamicHeaderMenu || $dynamicFooterMenu) {
            $sqlData[] = sprintf('CREATE TABLE %s (field_name varchar(200) DEFAULT NULL);', $GLOBALS['TCA']['pages']['columns'][$dynamicHeaderMenu ? 'header_menu' : 'footer_menu']['config']['MM']);
        }

        $event->setSqlData($sqlData);
    }
}
