<?php

$disableAssetHandling = (bool) ($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['vhs']['disableAssetHandling'] ?? false);

return [
    'frontend' => [
        'fluidtypo3/vhs/asset-inclusion' => [
            'target' => \FluidTYPO3\Vhs\Middleware\AssetInclusion::class,
            'after' => [
                'typo3/cms-frontend/content-length-headers',
                'typo3/cms-frontend/csp-headers',
            ],
            'disabled' => $disableAssetHandling,
        ],
        'fluidtypo3/vhs/request-availability' => [
            'target' => \FluidTYPO3\Vhs\Middleware\RequestAvailability::class,
            'before' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
        ],
    ],
];
