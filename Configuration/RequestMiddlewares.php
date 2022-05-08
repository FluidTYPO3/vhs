<?php
return [
    'frontend' => [
        'fluidtypo3/vhs/asset-inclusion' => [
            'target' => \FluidTYPO3\Vhs\Middleware\AssetInclusion::class,
            'after' => [
                'typo3/cms-core/response-propagation',
            ],
        ],
    ],
];
