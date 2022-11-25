<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class UnsetViewHelperTest
 */
class UnsetViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function canUnsetVariable()
    {
        $variables = new \ArrayObject(['test' => 'test']);
        $instance = $this->buildViewHelperInstance(['name' => 'test']);
        $this->templateVariableContainer->add('test', 'test');
        $instance->initializeArgumentsAndRender();
        $this->assertNotContains('test', $this->templateVariableContainer->getAll());
    }
}
