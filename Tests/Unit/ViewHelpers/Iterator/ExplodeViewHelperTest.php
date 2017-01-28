<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\ViewHelpers\Iterator\ExplodeViewHelper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer;

/**
 * Class ExplodeViewHelperTest
 */
class ExplodeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function explodesString()
    {
        $arguments = ['content' => '1,2,3', 'glue' => ','];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals(['1', '2', '3'], $result);
    }

    /**
     * @test
     */
    public function supportsCustomGlue()
    {
        $arguments = ['content' => '1;2;3', 'glue' => ';'];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals(['1', '2', '3'], $result);
    }

    /**
     * @test
     */
    public function supportsConstantsGlue()
    {
        $arguments = ['content' => "1\n2\n3", 'glue' => 'constant:LF'];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals(['1', '2', '3'], $result);
    }

    /**
     * @test
     */
    public function passesThroughUnknownSpecialGlue()
    {
        $arguments = ['content' => '1-2-3', 'glue' => 'unknown:-'];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals(['1', '2', '3'], $result);
    }
}
