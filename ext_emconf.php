<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'TYPO3 Default setup',
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
	'version' => '1.0.0-dev',
	'constraints' => array(
		'depends' => array(
			'typo3' => '8.7.0-8.99.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
