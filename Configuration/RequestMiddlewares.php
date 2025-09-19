<?php
return [
    'frontend' => [
        'fluidtypo3/vhs/asset-inclusion' => [
            'target' => \FluidTYPO3\Vhs\Middleware\AssetInclusion::class,
            'after' => [
                'typo3/cms-frontend/content-length-headers',
            ],
            'disabled' => filter_var($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['vhs']['setup']['disableAssetHandling'] ?? false, \FILTER_VALIDATE_BOOL),
        ],
        'fluidtypo3/vhs/request-availability' => [
            'target' => \FluidTYPO3\Vhs\Middleware\RequestAvailability::class,
            'before' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
        ],
    ],
];
