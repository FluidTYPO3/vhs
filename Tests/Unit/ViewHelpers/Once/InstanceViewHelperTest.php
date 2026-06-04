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
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

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
    public function testGetIdentifier($identifierArgument, $expectedIdentifier): void
    {
        $extbaseParameters = (new ExtbaseRequestParameters())
            ->setControllerActionName('action')
            ->setControllerName('Controller')
            ->setControllerExtensionName('Vhs')
            ->setPluginName('Plugin');
        $request = (new ServerRequest())->withAttribute('extbase', $extbaseParameters);
        $renderingContext = $this->getMockBuilder(RenderingContextInterface::class)
            ->addMethods(['getRequest'])
            ->getMockForAbstractClass();
        $renderingContext->method('getRequest')->willReturn($request);

        $instance = $this->createInstance();
        $this->setInaccessiblePropertyValue($instance, 'currentRenderingContext', $renderingContext);
        $result = $this->callInaccessibleMethod($instance, 'getIdentifier', ['identifier' => $identifierArgument]);
        $this->assertEquals($expectedIdentifier, $result);
    }

    /**
     * @return array
     */
    public static function getIdentifierTestValues(): array
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
    public function testStoreIdentifier(): void
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
    public function testAssertShouldSkip(): void
    {
        $instance = $this->createInstance();
        $instance->setArguments(['identifier' => 'test']);
        $this->assertFalse($this->callInaccessibleMethod($instance, 'assertShouldSkip', ['identifier' => 'test']));
        $GLOBALS[get_class($instance)]['test'] = true;
        $this->assertTrue($this->callInaccessibleMethod($instance, 'assertShouldSkip', ['identifier' => 'test']));
        unset($GLOBALS[get_class($instance)]['test']);
    }
}
