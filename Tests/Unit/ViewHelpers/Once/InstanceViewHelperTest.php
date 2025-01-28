<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Once;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/**
 * Class InstanceViewHelperTest
 */
class InstanceViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @dataProvider getIdentifierTestValues
     * @param string|NULL $identifierArgument
     * @param string $expectedIdentifier
     */
    public function testGetIdentifier($identifierArgument, $expectedIdentifier)
    {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '12.4', '>=')) {
            $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        } else {
            $request = $this->getMockBuilder(Request::class)
                ->disableOriginalConstructor()
                ->onlyMethods(
                    [
                        'getControllerActionName',
                        'getControllerName',
                        'getControllerObjectName',
                        'getControllerExtensionName',
                        'getPluginName',
                    ]
                )
                ->getMock();
        }

        $request->method('getControllerActionName')->willReturn('action');
        $request->method('getControllerName')->willReturn('Controller');
        $request->method('getControllerObjectName')->willReturn('Controller');
        $request->method('getControllerExtensionName')->willReturn('Vhs');
        $request->method('getPluginName')->willReturn('Plugin');
        if (method_exists(RenderingContext::class, 'getRequest')) {
            $renderingContext = $this->getMockBuilder(RenderingContext::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['getRequest'])
                ->getMock();
        } else {
            $renderingContext = $this->getMockBuilder(RenderingContext::class)
                ->disableOriginalConstructor()
                ->addMethods(['getRequest'])
                ->getMock();
        }

        $renderingContext->method('getRequest')->willReturn($request);

        $instance = $this->createInstance();
        $this->setInaccessiblePropertyValue($instance, 'currentRenderingContext', $renderingContext);
        $result = $this->callInaccessibleMethod($instance, 'getIdentifier', ['identifier' => $identifierArgument]);
        $this->assertEquals($expectedIdentifier, $result);
    }

    /**
     * @return array
     */
    public function getIdentifierTestValues()
    {
        return [
            [null, 'action_Controller_Plugin_Vhs'],
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
