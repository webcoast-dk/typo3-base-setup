<?php

namespace WEBcoast\Typo3BaseSetup\Install;


use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

class FileReferenceWizard extends AbstractUpdate
{
    protected $title = 'File references: Migrate references for "image" content element to field "assets"';

    /**
     * Checks whether updates are required.
     *
     * @param string &$description The description for the update
     *
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description)
    {
        if ($this->isWizardDone()) {
            return false;
        }

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content');
        $query = $connection->createQueryBuilder()->count('r.uid')->from('tt_content', 'c')->from(
            'sys_file_reference',
            'r'
        );
        $query->getRestrictions()->removeAll();
        $count = $query->where(
            'c.deleted=0',
            'r.deleted=0',
            'c.uid=r.uid_foreign',
            'r.tablenames="tt_content"',
            'r.fieldName="image"',
            'c.CType="image"'
        )->execute()->fetchColumn(0);

        if ($count > 0) {
            $description = sprintf('There are %d file references that need to be updated.', $count);

            return true;
        } else {
            $this->markWizardAsDone();

            return false;
        }
    }

    /**
     * Performs the accordant updates.
     *
     * @param array  &$dbQueries     Queries done in this update
     * @param string &$customMessage Custom message
     *
     * @return bool Whether everything went smoothly or not
     */
    public function performUpdate(array &$dbQueries, &$customMessage)
    {
        $result = true;
        $changedRows = 0;
        $errors = [];
        $contentConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content');
        $referenceConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_file_reference');
        $query = $contentConnection->createQueryBuilder()->select('r.uid', 'r.uid_foreign')->from('tt_content', 'c')->from(
            'sys_file_reference',
            'r'
        );
        $query->getRestrictions()->removeAll();
        $query->where(
            'c.deleted=0',
            'r.deleted=0',
            'c.uid=r.uid_foreign',
            'r.tablenames="tt_content"',
            'r.fieldName="image"',
            'c.CType="image"'
        );
        if ($statement = $query->execute()) {
            $statement->setFetchMode(\PDO::FETCH_ASSOC);
            foreach ($statement as $record) {
                try {
                    $affectedRows = 0;
                    $updateQuery = $referenceConnection->createQueryBuilder()->update('sys_file_reference')->set('fieldname', 'assets')->where(
                        'tablenames="tt_content"',
                        'uid=' . (int)$record['uid'],
                        'deleted=0'
                    );
                    $databaseQueries[] = $updateQuery->getSQL();
                    $affectedRows = $updateQuery->execute();
                    $referenceCountQuery = $referenceConnection->createQueryBuilder()->count('uid')->from('sys_file_reference');
                    $referenceCountQuery->getRestrictions()->removeAll();
                    $referenceCountQuery->where(
                        'tablenames="tt_content"',
                        'uid_foreign=' . (int)$record['uid_foreign'],
                        'deleted=0'
                    );
                    $referenceCount = $referenceCountQuery->execute()->fetchColumn(0);
                    $contentConnection->createQueryBuilder()->update('tt_content')
                        ->set('assets', $referenceCount)
                        ->set('image', 0)
                        ->where('uid=' . (int)$record['uid_foreign'])->execute();
                    if ($affectedRows === 1) {
                        ++$changedRows;
                    } else {
                        $result = false;
                    }
                } catch (\Exception $e) {
                    $result = false;
                    $errors[] = $e->getMessage();
                }
            }
        }
        if ($result) {
            $customMessage = sprintf(
                'Old content elements have been migrated: %d have been converted to new records.',
                $changedRows
            );
            $this->markWizardAsDone();
        } else {
            $customMessage = sprintf(
                'The execution was not successful. %d records have been changed. The following errors occured: <br /><br />%s',
                $changedRows,
                implode('<br /><br/>', $errors)
            );
        }

        return $result;
    }
}