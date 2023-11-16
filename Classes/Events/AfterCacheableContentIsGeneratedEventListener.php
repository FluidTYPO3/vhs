<?php

namespace FluidTYPO3\Vhs\Events;

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
