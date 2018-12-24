<?php

namespace WEBcoast\Typo3BaseSetup\Install;

use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

abstract class AbstractWizard implements UpgradeWizardInterface, ChattyInterface {

	/**
	 * @var string
	 */
	protected $title = '';

	/**
	 * @var string
	 */
	protected $description = '';

	/**
	 * Allow status output when showing available wizards
	 *
	 * @var string|null
	 */
	protected $status = null;

	/**
	 * @var OutputInterface
	 */
	protected $output = null;
	/**
	 * Setter injection for output into upgrade wizards
	 *
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 */
	public function setOutput(OutputInterface $output): void
	{
		$this->output = $output;
	}

	/**
	 * Return the speaking name of this wizard
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * Return the description for this wizard or the status text, if set
	 *
	 * @return string
	 */
	public function getDescription(): string
	{
		if ($this->status !== null) {
			return $this->status;
		}
		return $this->description;
	}

	public function getPrerequisites(): array
	{
		return [];
	}

	/**
	 * Return the identifier for this wizard
	 * This should be the same string as used in the ext_localconf class registration
	 *
	 * @return string
	 */
	public function getIdentifier(): string
	{
		return static::class;
	}

	/**
	 * Marks some wizard as being "seen" so that it not shown again.
	 *
	 * Writes the info in LocalConfiguration.php
	 *
	 * @param mixed $confValue The configuration is set to this value
	 */
	protected function markWizardAsDone($confValue = 1)
	{
		GeneralUtility::makeInstance(Registry::class)->set('installUpdate', $this->getIdentifier(), $confValue);
	}

	/**
	 * Checks if this wizard has been "done" before
	 *
	 * @return bool TRUE if wizard has been done before, FALSE otherwise
	 */
	protected function isWizardDone()
	{
		return GeneralUtility::makeInstance(Registry::class)->get('installUpdate', $this->getIdentifier(), false);
	}
}
