<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper;

class DeferredViewHelperTest extends AbstractViewHelperTestCase
{
    public function testOutputsEmptyStringWithoutDeferredStringContext(): void
    {
        $this->viewHelperVariableContainer->addAll(
            AbstractMenuViewHelper::class,
            [
                'deferredArray' => ['foo' => 'bar'],
            ]
        );
        $output = $this->executeViewHelper();
        self::assertSame('', $output);
    }

    public function testOutputsEmptyStringWithoutDeferredArrayContext(): void
    {
        $this->viewHelperVariableContainer->addAll(
            AbstractMenuViewHelper::class,
            [
                'deferredString' => 'deferredString',
            ]
        );
        $output = $this->executeViewHelper();
        self::assertSame('', $output);
    }

    public function testThrowsExceptionWithEmptyAsArgument(): void
    {
        $this->viewHelperVariableContainer->addAll(
            AbstractMenuViewHelper::class,
            [
                'deferredString' => 'deferredString',
                'deferredArray' => ['foo' => 'bar'],
            ]
        );
        self::expectViewHelperException();
        $this->executeViewHelper(['as' => '']);
    }

    public function testOutputsDeferredStringWithoutAsArgument(): void
    {
        $this->viewHelperVariableContainer->addAll(
            AbstractMenuViewHelper::class,
            [
                'deferredString' => 'deferredString',
                'deferredArray' => ['foo' => 'bar'],
            ]
        );
        $output = $this->executeViewHelper();
        self::assertSame('deferredString', $output);
    }

    public function testRendersContentWithDeferredArray(): void
    {
        $this->viewHelperVariableContainer->addAll(
            AbstractMenuViewHelper::class,
            [
                'deferredString' => 'deferredString',
                'deferredArray' => ['foo' => 'bar'],
            ]
        );
        $output = $this->executeViewHelper(['as' => 'menu'], [], $this->createObjectAccessorNode('menu.foo'));
        self::assertSame('bar', $output);
    }

    public function testBacksUpExistingVariableAndRendersContentWithDeferredArray(): void
    {
        $this->viewHelperVariableContainer->addAll(
            AbstractMenuViewHelper::class,
            [
                'deferredString' => 'deferredString',
                'deferredArray' => ['foo' => 'bar'],
            ]
        );
        $output = $this->executeViewHelper(
            ['as' => 'menu'],
            ['menu' => 'original'],
            $this->createObjectAccessorNode('menu.foo')
        );
        self::assertSame('bar', $output);
        self::assertSame('original', $this->templateVariableContainer->get('menu'));
    }
}
