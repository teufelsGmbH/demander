<?php

return [
    'frontend' => [
        'demander-request' => [
            'target' => \Pixelant\Demander\Middlewares\DemanderRequestMiddleware::class,
            'after' => [
                'typo3/cms-frontend/page-argument-validator',
            ],
            'before' => [
                'yoast-seo-page-request',
            ],
        ],
    ],
];
