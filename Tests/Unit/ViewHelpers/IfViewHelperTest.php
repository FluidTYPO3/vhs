<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * @protection off
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 */
class IfViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function rendersThenChildWithFlatComparison() {
		$stack = array(array('foo'), '==', array('foo'));
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack)));
	}

	/**
	 * @test
	 */
	public function rendersThenChildWithPrecedence() {
		$stack = array(1, 'OR', 0, 'AND', 0);
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithFlatArrayComparison() {
		$stack = array(array('foo'), '==', '3');
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack)));
	}

	/**
	 * @test
	 */
	public function rendersThenChildWithFlatLogicalOperator() {
		$stack = array(1, '==', 1, 'AND', 1);
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack)));
	}

	/**
	 * @test
	 */
	public function rendersThenChildWithRightStack() {
		$stack = array(1, '==', 1, 'AND', array(1, '!=', 0));
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack)));
	}

	/**
	 * @test
	 */
	public function rendersThenChildWithStacks() {
		$stack = array(array('foo', '!=', 'bar'), 'AND', 1, 'OR', array(1, '==', '0'));
		$this->assertEquals('then', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithStacks() {
		$stack = array(array('foo', '!=', 'bar'), 'AND', array('foo', '==', 'bar'));
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithEmptyStack() {
		$stack = array();
		$this->assertEquals('else', $this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack)));
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithNoLogicalOperator() {
		$this->setExpectedException('RuntimeException', NULL, 1385071197);
		$stack = array(array('foo', '!=', 'bar'), array('foo', '==', 'bar'));
		$this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack));
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithWrongLogicalOperatorOrder() {
		$this->setExpectedException('RuntimeException', NULL, 1385072228);
		$stack = array(array('foo', '!=', 'bar'), 'AND', 'AND', array('foo', '==', 'bar'));
		$this->executeViewHelper(array('then' => 'then', 'else' => 'else', 'stack' => $stack));
	}

	/**
	 * @test
	 */
	public function evaluateLogicalOperatorAnd() {
		$instance = $this->createInstance();
		$this->assertEquals(FALSE, $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', array(TRUE), 'AND', array(FALSE)));
	}

	/**
	 * @test
	 */
	public function evaluateLogicalOperatorOr() {
		$instance = $this->createInstance();
		$this->assertEquals(TRUE, $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', array(TRUE), 'OR', array(FALSE)));
	}

	/**
	 * @test
	 */
	public function evaluateLogicalOperatorInternalError() {
		$this->setExpectedException('RuntimeException', NULL, 1385072357);
		$instance = $this->createInstance();
		$this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', array(TRUE), 'foo', array(FALSE));
	}

	/**
	 * @test
	 */
	public function prepareSideForEvaluation() {
		$instance = $this->createInstance();
		$this->assertEquals(array(TRUE), $this->callInaccessibleMethod($instance, 'prepareSideForEvaluation', array(TRUE)));
	}

	/**
	 * @test
	 */
	public function prepareSideForEvaluationArray() {
		$instance = $this->createInstance();
		$this->assertEquals(array(TRUE), $this->callInaccessibleMethod($instance, 'prepareSideForEvaluation', array(array(TRUE))));
	}

}
