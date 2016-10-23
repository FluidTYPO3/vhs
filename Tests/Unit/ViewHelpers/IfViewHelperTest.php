<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class IfViewHelperTest
 */
class IfViewHelperTest extends AbstractViewHelperTest
{
    /**
     * @return void
     */
    public function setUp()
    {
        if (class_exists(\TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper::class)) {
            $this->markTestSkipped('Test not executed on TYPO3v8 (ViewHelper is deprecated from this version and up)');
        }
        parent::setUp();
    }

    /**
     * @test
     */
    public function rendersThenChildWithFlatComparison()
    {
        $stack = [['foo'], '==', ['foo']];
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersThenChildWithPrecedence()
    {
        $stack = [1, 'OR', 0, 'AND', 0];
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersElseChildWithFlatArrayComparison()
    {
        $stack = [['foo'], '==', '3'];
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersThenChildWithFlatLogicalOperator()
    {
        $stack = [1, '==', 1, 'AND', 1];
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersThenChildWithRightStack()
    {
        $stack = [1, '==', 1, 'AND', [1, '!=', 0]];
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersThenChildWithStacks()
    {
        $stack = [['foo', '!=', 'bar'], 'AND', 1, 'OR', [1, '==', '0']];
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersElseChildWithStacks()
    {
        $stack = [['foo', '!=', 'bar'], 'AND', ['foo', '==', 'bar']];
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersElseChildWithEmptyStack()
    {
        $stack = [];
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersElseChildWithNoLogicalOperator()
    {
        $this->setExpectedException('RuntimeException', null, 1385071197);
        $stack = [['foo', '!=', 'bar'], ['foo', '==', 'bar']];
        $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]);
    }

    /**
     * @test
     */
    public function rendersElseChildWithWrongLogicalOperatorOrder()
    {
        $this->setExpectedException('RuntimeException', null, 1385072228);
        $stack = [['foo', '!=', 'bar'], 'AND', 'AND', ['foo', '==', 'bar']];
        $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]);
    }

    /**
     * @test
     */
    public function evaluateLogicalOperatorAnd()
    {
        $instance = $this->createInstance();
        $this->assertEquals(false, $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', [true], 'AND', [false]));
    }

    /**
     * @test
     */
    public function evaluateLogicalOperatorOr()
    {
        $instance = $this->createInstance();
        $this->assertEquals(true, $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', [true], 'OR', [false]));
    }

    /**
     * @test
     */
    public function evaluateLogicalOperatorInternalError()
    {
        $this->setExpectedException('RuntimeException', null, 1385072357);
        $instance = $this->createInstance();
        $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', [true], 'foo', [false]);
    }

    /**
     * @test
     */
    public function prepareSideForEvaluation()
    {
        $instance = $this->createInstance();
        $this->assertEquals([true], $this->callInaccessibleMethod($instance, 'prepareSideForEvaluation', [true]));
    }

    /**
     * @test
     */
    public function prepareSideForEvaluationArray()
    {
        $instance = $this->createInstance();
        $this->assertEquals([true], $this->callInaccessibleMethod($instance, 'prepareSideForEvaluation', [[true]]));
    }
}
