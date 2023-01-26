<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class DummyTypoScriptFrontendController extends TypoScriptFrontendController
{
    public function __construct()
    {
        $this->id = 1;
    }
}
