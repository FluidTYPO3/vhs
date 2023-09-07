<?php
return [
    'frontend' => [
        'fluidtypo3/vhs/asset-inclusion' => [
            'target' => \FluidTYPO3\Vhs\Middleware\AssetInclusion::class,
            'after' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
                'typo3/cms-frontend/prepare-tsfe-rendering',
                'fluidtypo3/vhs/request-availability',
            ],
            'before' => [
                'typo3/cms-frontend/content-length-headers',
                'typo3/cms-frontend/output-compression',
            ],
        ],
        'fluidtypo3/vhs/request-availability' => [
            'target' => \FluidTYPO3\Vhs\Middleware\RequestAvailability::class,
            'before' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
        ],
    ],
];
