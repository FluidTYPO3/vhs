<?php

namespace FluidTYPO3\Vhs\Events;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\AssetService;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

class AfterCacheableContentIsGeneratedEventListener
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function insertVhsAssetHeaderAndFooterCode(AfterCacheableContentIsGeneratedEvent $event): void
    {
        $this->assetService->buildAll([], $event->getController(), $event->isCachingEnabled());
    }
}
