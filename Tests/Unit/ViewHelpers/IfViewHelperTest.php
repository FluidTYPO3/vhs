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
     * @test
     */
    public function rendersThenChildWithFlatComparison()
    {
        $stack = array(array('foo'), '==', array('foo'));
        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        );
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
        $stack = array(1, 'OR', 0, 'AND', 0);
        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        );
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
        $stack = array(array('foo'), '==', '3');
        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        );
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
        $stack = array(1, '==', 1, 'AND', 1);
        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        );
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
        $stack = array(1, '==', 1, 'AND', array(1, '!=', 0));
        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        );
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
        $stack = array(array('foo', '!=', 'bar'), 'AND', 1, 'OR', array(1, '==', '0'));
        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        );
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
        $stack = array(array('foo', '!=', 'bar'), 'AND', array('foo', '==', 'bar'));
        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        );
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
        $stack = array();
        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'stack' => $stack
        );
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
        $stack = array(array('foo', '!=', 'bar'), array('foo', '==', 'bar'));
        $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack));
    }

    /**
     * @test
     */
    public function rendersElseChildWithWrongLogicalOperatorOrder()
    {
        $this->setExpectedException('RuntimeException', null, 1385072228);
        $stack = array(array('foo', '!=', 'bar'), 'AND', 'AND', array('foo', '==', 'bar'));
        $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack));
    }

    /**
     * @test
     */
    public function evaluateLogicalOperatorAnd()
    {
        $instance = $this->createInstance();
        $this->assertEquals(false, $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', array(true), 'AND', array(false)));
    }

    /**
     * @test
     */
    public function evaluateLogicalOperatorOr()
    {
        $instance = $this->createInstance();
        $this->assertEquals(true, $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', array(true), 'OR', array(false)));
    }

    /**
     * @test
     */
    public function evaluateLogicalOperatorInternalError()
    {
        $this->setExpectedException('RuntimeException', null, 1385072357);
        $instance = $this->createInstance();
        $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', array(true), 'foo', array(false));
    }

    /**
     * @test
     */
    public function prepareSideForEvaluation()
    {
        $instance = $this->createInstance();
        $this->assertEquals(array(true), $this->callInaccessibleMethod($instance, 'prepareSideForEvaluation', array(true)));
    }

    /**
     * @test
     */
    public function prepareSideForEvaluationArray()
    {
        $instance = $this->createInstance();
        $this->assertEquals(array(true), $this->callInaccessibleMethod($instance, 'prepareSideForEvaluation', array(array(true))));
    }
}
