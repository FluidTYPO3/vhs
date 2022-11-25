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
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class SetViewHelperTest
 */
class SetViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function canSetVariable()
    {
        $variables = ['test' => true];
        $this->executeViewHelper(['name' => 'test', 'value' => false], $variables);
        $this->assertFalse($this->templateVariableContainer->get('test'));
    }

    /**
     * @test
     */
    public function canSetVariableInExistingArrayValue()
    {
        $variables = ['test' => ['test' => true]];
        $this->executeViewHelper(['name' => 'test.test', 'value' => false], $variables);
        $this->assertFalse($this->templateVariableContainer->get('test.test'));
    }

    /**
     * @test
     */
    public function ignoresNestedVariableIfRootDoesNotExist()
    {
        $result = $this->executeViewHelper(['name' => 'doesnotexist.test', 'value' => false]);
        $this->assertNull($this->templateVariableContainer->get('test.test'));
    }

    /**
     * @test
     */
    public function ignoresNestedVariableIfRootDoesNotAllowSetting()
    {
        $domainObject = new Foo();
        $variables = ['test' => $domainObject];
        $result = $this->executeViewHelper(['name' => 'test.propertydoesnotexist', 'value' => false], $variables);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ignoresNestedVariableIfRootPropertyNameIsInvalid()
    {
        $variables = ['test' => 'test'];
        $result = $this->executeViewHelper(['name' => 'test.test', 'value' => false], $variables);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function canSetVariableWithValueFromTagContent()
    {
        $variables = ['test' => true];
        $this->executeViewHelperUsingTagContent(false, ['name' => 'test'], $variables);
        $this->assertFalse($this->templateVariableContainer->get('test'));
    }
}
