<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use FluidTYPO3\Vhs\Traits\SlideViewHelperTrait;

class DummySlideViewHelperTraitViewHelper
{
    use SlideViewHelperTrait;

    public array $arguments = [];

    protected function getSlideRecordsFromPage(int $pageUid, ?int $limit): array
    {
        return [];
    }

    private function registerArgument(
        string $name,
        string $type,
        string $description,
        bool $required = false,
        $default = null
    ): void {
    }
}
