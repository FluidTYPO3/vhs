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
    public function canSetVariableNestedOneLevelInArrayValue()
    {
        $variables = new \ArrayObject(['test' => ['test1' => ['test2' => true]]]);
        $this->executeViewHelper(['name' => 'test.test1.test2', 'value' => false], $variables);
        $this->assertFalse($variables['test']['test1']['test2']);
    }

    /**
     * @test
     */
    public function canSetVariableNestedTwoLevelsInArrayValue()
    {
        $variables = new \ArrayObject(['test' => ['test1' => ['test2' => ['test3' => true]]]]);
        $this->executeViewHelper(['name' => 'test.test1.test2.test3', 'value' => false], $variables);
        $this->assertFalse($variables['test']['test1']['test2']['test3']);
    }

    /**
     * @test
     */
    public function canSetVariableInObject()
    {
        $variables = new \ArrayObject(['test' => (object) ['test' => true]]);
        $this->executeViewHelper(['name' => 'test.test', 'value' => false], $variables);
        $this->assertFalse($variables['test']->test);
    }

    /**
     * @test
     */
    public function canSetVariableInArrayNestedInObject()
    {
        $variables = new \ArrayObject(['test' => (object) ['test1' => ['test2' => true]]]);
        $this->executeViewHelper(['name' => 'test.test1.test2', 'value' => false], $variables);
        $this->assertFalse($variables['test']->test1['test2']);
    }

    /**
     * @test
     */
    public function canSetVariableNestedInArrayNestedInObject()
    {
        $variables = new \ArrayObject(['test' => (object) ['test1' => ['test2' => ['test3' => true]]]]);
        $this->executeViewHelper(['name' => 'test.test1.test2.test3', 'value' => false], $variables);
        $this->assertFalse($variables['test']->test1['test2']['test3']);
    }

    /**
     * @test
     */
    public function canSetVariableInObjectNestedInArrayNestedInObject()
    {
        $variables = new \ArrayObject(['test' => (object) ['test1' => ['test2' => (object) ['test3' => true]]]]);
        $this->executeViewHelper(['name' => 'test.test1.test2.test3', 'value' => false], $variables);
        $this->assertFalse($variables['test']->test1['test2']->test3);
    }

    /**
     * @test
     */
    public function canSetVariableInArrayNestedInObjectNestedInArray()
    {
        $variables = new \ArrayObject(['test' => ['test1' => (object) ['test2' => ['test3' => true]]]]);
        $this->executeViewHelper(['name' => 'test.test1.test2.test3', 'value' => false], $variables);
        $this->assertFalse($variables['test']['test1']->test2['test3']);
    }

    /**
     * @test
     */
    public function canSetVariableInNestedArrayNestedInObjectNestedInArray()
    {
        $variables = new \ArrayObject(['test' => ['test1' => (object) ['test2' => ['test3' => ['test4' => true]]]]]);
        $this->executeViewHelper(['name' => 'test.test1.test2.test3.test4', 'value' => false], $variables);
        $this->assertFalse($variables['test']['test1']->test2['test3']['test4']);
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
    public function ignoresNestedVariableIfPathDoesNotExist() {
        $variables = new \ArrayObject(['test' => ['test' => ['test' => true]]]);
        $result = $this->executeViewHelper(['name' => 'test.doesnotexist.test.test', 'value' => false], $variables);
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
