<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/**
 * Class InstanceViewHelperTest
 */
class InstanceViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @dataProvider getIdentifierTestValues
     * @param string|NULL $identifierArgument
     * @param string $expectedIdentifier
     */
    public function testGetIdentifier($identifierArgument, $expectedIdentifier)
    {
        $instance = $this->createInstance();
        $instance->setArguments(['identifier' => $identifierArgument]);
        $renderingContext = new RenderingContext();
        $controllerContext = new ControllerContext();
        $request = new Request();
        $request->setControllerActionName('p1');
        $request->setControllerName('p2');
        $request->setPluginName('p3');
        $request->setControllerExtensionName('p4');
        $controllerContext->setRequest($request);
        $renderingContext->setControllerContext($controllerContext);
        $instance->setRenderingContext($renderingContext);
        $instance::renderStatic(['identifier' => $identifierArgument], function() { return ''; }, $renderingContext);
        $result = $this->callInaccessibleMethod($instance, 'getIdentifier', ['identifier' => $identifierArgument]);
        $this->assertEquals($expectedIdentifier, $result);
    }

    /**
     * @return array
     */
    public function getIdentifierTestValues()
    {
        return [
            [null, 'p1_p2_p3_p4'],
            ['test', 'test'],
            ['test2', 'test2'],
        ];
    }

    /**
     * @return void
     */
    public function testStoreIdentifier()
    {
        $instance = $this->createInstance();
        $instance->setArguments(['identifier' => 'test']);
        $this->callInaccessibleMethod($instance, 'storeIdentifier', ['identifier' => 'test']);
        $this->assertTrue($GLOBALS[get_class($instance)]['test']);
        unset($GLOBALS[get_class($instance)]['test']);
    }

    /**
     * @return void
     */
    public function testAssertShouldSkip()
    {
        $instance = $this->createInstance();
        $instance->setArguments(['identifier' => 'test']);
        $this->assertFalse($this->callInaccessibleMethod($instance, 'assertShouldSkip', ['identifier' => 'test']));
        $GLOBALS[get_class($instance)]['test'] = true;
        $this->assertTrue($this->callInaccessibleMethod($instance, 'assertShouldSkip', ['identifier' => 'test']));
        unset($GLOBALS[get_class($instance)]['test']);
    }
}
