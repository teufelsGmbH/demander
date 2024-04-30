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
    'version' => '0.3.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.6-12.4.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ]
];
