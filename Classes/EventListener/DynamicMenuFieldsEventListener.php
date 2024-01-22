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
            $sqlData[] = sprintf('CREATE TABLE %s (uid_local int(10) unsigned DEFAULT 0 NOT NULL, uid_foreign int(10) unsigned DEFAULT 0 NOT NULL, field_name varchar(200) DEFAULT NULL, PRIMARY KEY (uid_local, uid_foreign, field_name));', $GLOBALS['TCA']['pages']['columns'][$dynamicHeaderMenu ? 'header_menu' : 'footer_menu']['config']['MM']);

            if ($dynamicHeaderMenu) {
                $sqlData[] = sprintf('CREATE TABLE pages (%s int(11) DEFAULT NULL);', 'header_menu');
            }

            if ($dynamicFooterMenu) {
                $sqlData[] = sprintf('CREATE TABLE pages (%s int(11) DEFAULT NULL);', 'footer_menu');
            }
        }

        $event->setSqlData($sqlData);
    }
}
