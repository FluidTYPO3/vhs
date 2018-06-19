<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class ImplodeViewHelperTest
 */
class ImplodeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function implodesString()
    {
        $arguments = ['content' => ['1', '2', '3'], 'glue' => ','];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('1,2,3', $result);
    }

    /**
     * @test
     */
    public function supportsCustomGlue()
    {
        $arguments = ['content' => ['1', '2', '3'], 'glue' => ';'];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('1;2;3', $result);
    }
}
