<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyTemplateVariableViewHelper;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;

class TemplateVariableViewHelperTraitTest extends AbstractTestCase
{
    public function testWithoutAsArgument(): void
    {
        $variableProvider = $this->getMockBuilder(StandardVariableProvider::class)
            ->setMethods(['add', 'get'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $variableProvider->expects(self::never())->method('add');
        $variableProvider->expects(self::never())->method('get');

        $subject = new DummyTemplateVariableViewHelper();
        $subject->initializeArguments();
        $subject->templateVariableContainer = $variableProvider;

        self::assertSame('foobar', $subject->test('foobar'));
    }

    public function testWithAsArgument(): void
    {
        $variableProvider = $this->getMockBuilder(StandardVariableProvider::class)
            ->setMethods(['add', 'get'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $variableProvider->expects(self::once())->method('add')->with('as', 'foobar');
        $variableProvider->expects(self::never())->method('get');

        $subject = new DummyTemplateVariableViewHelper();
        $subject->initializeArguments();
        $subject->templateVariableContainer = $variableProvider;
        $subject->arguments['as'] = 'as';

        self::assertSame('', $subject->test('foobar'));
    }

    public function testWithoutAsArgumentStatic(): void
    {
        $variableProvider = $this->getMockBuilder(StandardVariableProvider::class)
            ->setMethods(['add', 'get'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $variableProvider->expects(self::never())->method('add');
        $variableProvider->expects(self::never())->method('get');

        $context = $this->getMockBuilder(RenderingContextInterface::class)->getMockForAbstractClass();
        $context->method('getVariableProvider')->willReturn($variableProvider);

        $closure = function () { return ''; };

        $output = DummyTemplateVariableViewHelper::testStatic('foobar', null, $context, $closure);
        self::assertSame('foobar', $output);
    }

    public function testWithAsArgumentStatic(): void
    {
        $variableProvider = $this->getMockBuilder(StandardVariableProvider::class)
            ->setMethods(['add', 'get'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $variableProvider->expects(self::once())->method('add')->with('as', 'foobar');
        $variableProvider->expects(self::never())->method('get');

        $context = $this->getMockBuilder(RenderingContextInterface::class)->getMockForAbstractClass();
        $context->method('getVariableProvider')->willReturn($variableProvider);

        $closure = function () { return ''; };

        $output = DummyTemplateVariableViewHelper::testStatic('foobar', 'as', $context, $closure);
        self::assertSame('', $output);
    }

    public function testBacksUpCurrentVariables(): void
    {
        $variables = ['as' => 'exists'];
        $variableProvider = new StandardVariableProvider($variables);

        $subject = new DummyTemplateVariableViewHelper();
        $subject->initializeArguments();
        $subject->templateVariableContainer = $variableProvider;
        $subject->arguments['as'] = 'as';

        $subject->test('foobar');
        self::assertSame($variables, $variableProvider->getAll());
    }
}
