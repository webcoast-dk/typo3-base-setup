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
	'version' => '2.0.0-dev',
	'constraints' => array(
		'depends' => array(
			'typo3' => '9.5.0-9.5.99',
            'fluid_styled_content' => '9.5.0-9.5.99'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
