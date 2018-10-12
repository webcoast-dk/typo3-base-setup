<?php

namespace WEBcoast\Typo3BaseSetup\Install;


use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\RepeatableInterface;

class FileReferenceWizard extends AbstractWizard implements RepeatableInterface
{
    protected $title = 'File references: Migrate references for "image" and "textpic" content element to field "assets"';

    protected $description = 'Migrate file references from field "image" to field "assets" for content types "textpic" and "image"';

    /**
     * Checks whether updates are required.
     *
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function updateNecessary(): bool
    {
        if ($this->isWizardDone()) {
            return false;
        }

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content');
        $query = $connection->createQueryBuilder()->count('r.uid')
            ->from('tt_content', 'c')
            ->join('c', 'sys_file_reference', 'r', 'c.uid=r.uid_foreign');
        $query->getRestrictions()->removeAll();
        $count = $query->where(
            'c.deleted=0',
            'r.deleted=0',
            'r.tablenames="tt_content"',
            'r.fieldName="image"',
            $query->expr()->orX(
                'c.CType="image"',
                'c.CType="textpic"'
            )
        )->execute()->fetchColumn(0);

        if ($count > 0) {
            $this->output->write(sprintf('There are %d file references that need to be updated.', $count));

            return true;
        } else {
            $this->markWizardAsDone();

            return false;
        }
    }

    /**
     * Performs the accordant updates.
     *
     * @return bool Whether everything went smoothly or not
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeUpdate(): bool
    {
        $result = true;
        $changedRows = 0;
        $errors = [];
        $contentConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content');
        $referenceConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_file_reference');
        $query = $contentConnection->createQueryBuilder()->select('r.uid', 'r.uid_foreign')
            ->from('tt_content', 'c')
            ->join('c', 'sys_file_reference', 'r', 'c.uid=r.uid_foreign');
        $query->getRestrictions()->removeAll();
        $query->where(
            'c.deleted=0',
            'r.deleted=0',
            'r.tablenames="tt_content"',
            'r.fieldName="image"',
            $query->expr()->orX(
                'c.CType="image"',
                'c.CType="textpic"'
            )
        );
        if ($statement = $query->execute()) {
            $statement->setFetchMode(\PDO::FETCH_ASSOC);
            foreach ($statement as $record) {
                $referenceConnection->beginTransaction();
                try {
                    $updateQuery = $referenceConnection->createQueryBuilder()->update('sys_file_reference')->set('fieldname', 'assets')->where(
                        'tablenames="tt_content"',
                        'uid=' . (int)$record['uid'],
                        'deleted=0'
                    );
                    $databaseQueries[] = $updateQuery->getSQL();
                    $updateQuery->execute();
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
                    $referenceConnection->commit();
                    $result = true;
                    $changedRows++;
                } catch (\Exception $e) {
                    $result = false;
                    $errors[] = $e->getMessage();
                    $referenceConnection->rollBack();
                }
            }
        }
        if ($result) {
            $this->output->write(sprintf(
                'Old content elements have been migrated: %d have been converted to new records.',
                $changedRows
            ));
            $this->markWizardAsDone();
        } else {
            $this->output->write(sprintf(
                'The execution was not successful. %d records have been changed. The following errors occured: <br /><br />%s',
                $changedRows,
                implode('<br /><br/>', $errors)
            ));
        }

        return $result;
    }
}
