<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Demander',
    'description' => 'Configurable, demand-based filtering framework with permalink-support for TYPO3.',
    'category' => 'plugin',
    'author' => 'Pixelant',
    'author_email' => 'info@pixelant.net',
    'author_company' => 'Pixelant',
    'state' => 'alpha',
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'version' => '0.2.0-alpha',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.6-11.5.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ]
];
