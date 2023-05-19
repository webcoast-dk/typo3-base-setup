<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 base setup',
    'description' => 'TYPO3 base setup and components',
    'version' => '5.0.0-dev',
    'category' => 'frontend',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'author' => 'Thorben Nissen',
    'author_email' => 'thorben@webcoast.dk',
    'author_company' => 'WEBcoast',
    'autoload' => [
        'psr-4' => [
            'WEBcoast\\Typo3BaseSetup\\' => 'Classes'
        ]
    ]
];
