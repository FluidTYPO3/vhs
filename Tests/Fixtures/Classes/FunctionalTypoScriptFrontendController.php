<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

class FunctionalTypoScriptFrontendController
{
    public int $id = 123;
    public array $register = [];
    public object $tmpl;

    public function __construct()
    {
        $this->tmpl = (object) [
            'setup' => [],
        ];
    }
}
