<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use FluidTYPO3\Vhs\Traits\SourceSetViewHelperTrait;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class DummySourceSetViewHelper
{
    use SourceSetViewHelperTrait;

    public ?ConfigurationManagerInterface $configurationManager = null;
    public array $arguments = [];

    public static function preprocessSourceUri(string $src, array $arguments): string
    {
        return 'processed';
    }
}
