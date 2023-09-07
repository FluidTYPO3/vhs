<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use FluidTYPO3\Vhs\Traits\SourceSetViewHelperTrait;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class DummySourceSetViewHelper
{
    use SourceSetViewHelperTrait;

    public ?ContentObjectRenderer $contentObject = null;
    public array $arguments = [];

    public static function preprocessSourceUri(string $src, array $arguments): string
    {
        return 'processed';
    }
}
