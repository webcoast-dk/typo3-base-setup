<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'TYPO3 base setup',
	'description' => 'TYPO3 base setup and components',
	'category' => 'plugin',
	'author' => 'Thorben Nissen',
	'author_email' => 'thorben.nissen@kapp-hamburg.de',
	'author_company' => '',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '4.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '10.4.0-11.5.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
