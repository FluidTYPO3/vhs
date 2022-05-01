<?php
return [
    'frontend' => [
        'fluidtypo3/vhs/asset-inclusion' => [
            'target' => \FluidTYPO3\Vhs\Middleware\AssetInclusion::class,
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
            'after' => [
                'typo3/cms-frontend/output-compression',
            ],
        ],
    ],
];
