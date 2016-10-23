<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class SetViewHelperTest
 */
class SetViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canSetVariable()
    {
        $variables = new \ArrayObject(['test' => true]);
        $this->executeViewHelper(['name' => 'test', 'value' => false], $variables);
        $this->assertFalse($variables['test']);
    }

    /**
     * @test
     */
    public function canSetVariableInExistingArrayValue()
    {
        $variables = new \ArrayObject(['test' => ['test' => true]]);
        $this->executeViewHelper(['name' => 'test.test', 'value' => false], $variables);
        $this->assertFalse($variables['test']['test']);
    }

    /**
     * @test
     */
    public function ignoresNestedVariableIfRootDoesNotExist()
    {
        $variables = new \ArrayObject(['test' => ['test' => true]]);
        $result = $this->executeViewHelper(['name' => 'doesnotexist.test', 'value' => false], $variables);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ignoresNestedVariableIfRootDoesNotAllowSetting()
    {
        $domainObject = new Foo();
        $variables = new \ArrayObject(['test' => $domainObject]);
        $result = $this->executeViewHelper(['name' => 'test.propertydoesnotexist', 'value' => false], $variables);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ignoresNestedVariableIfRootPropertyNameIsInvalid()
    {
        $variables = new \ArrayObject(['test' => 'test']);
        $result = $this->executeViewHelper(['name' => 'test.test', 'value' => false], $variables);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function canSetVariableWithValueFromTagContent()
    {
        $variables = new \ArrayObject(['test' => true]);
        $this->executeViewHelperUsingTagContent(false, ['name' => 'test'], $variables);
        $this->assertFalse($variables['test']);
    }
}
