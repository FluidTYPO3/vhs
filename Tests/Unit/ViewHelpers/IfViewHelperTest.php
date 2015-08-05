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
		$stack = [['foo'], '==', ['foo']];
		$this->assertEquals('then', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]));
	}

	/**
	 * @test
	 */
	public function rendersThenChildWithPrecedence() {
		$stack = [1, 'OR', 0, 'AND', 0];
		$this->assertEquals('then', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]));
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithFlatArrayComparison() {
		$stack = [['foo'], '==', '3'];
		$this->assertEquals('else', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]));
	}

	/**
	 * @test
	 */
	public function rendersThenChildWithFlatLogicalOperator() {
		$stack = [1, '==', 1, 'AND', 1];
		$this->assertEquals('then', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]));
	}

	/**
	 * @test
	 */
	public function rendersThenChildWithRightStack() {
		$stack = [1, '==', 1, 'AND', [1, '!=', 0]];
		$this->assertEquals('then', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]));
	}

	/**
	 * @test
	 */
	public function rendersThenChildWithStacks() {
		$stack = [['foo', '!=', 'bar'], 'AND', 1, 'OR', [1, '==', '0']];
		$this->assertEquals('then', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]));
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithStacks() {
		$stack = [['foo', '!=', 'bar'], 'AND', ['foo', '==', 'bar']];
		$this->assertEquals('else', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]));
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithEmptyStack() {
		$stack = [];
		$this->assertEquals('else', $this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]));
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithNoLogicalOperator() {
		$this->setExpectedException('RuntimeException', NULL, 1385071197);
		$stack = [['foo', '!=', 'bar'], ['foo', '==', 'bar']];
		$this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]);
	}

	/**
	 * @test
	 */
	public function rendersElseChildWithWrongLogicalOperatorOrder() {
		$this->setExpectedException('RuntimeException', NULL, 1385072228);
		$stack = [['foo', '!=', 'bar'], 'AND', 'AND', ['foo', '==', 'bar']];
		$this->executeViewHelper(['then' => 'then', 'else' => 'else', 'stack' => $stack]);
	}

	/**
	 * @test
	 */
	public function evaluateLogicalOperatorAnd() {
		$instance = $this->createInstance();
		$this->assertEquals(FALSE, $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', [TRUE], 'AND', [FALSE]));
	}

	/**
	 * @test
	 */
	public function evaluateLogicalOperatorOr() {
		$instance = $this->createInstance();
		$this->assertEquals(TRUE, $this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', [TRUE], 'OR', [FALSE]));
	}

	/**
	 * @test
	 */
	public function evaluateLogicalOperatorInternalError() {
		$this->setExpectedException('RuntimeException', NULL, 1385072357);
		$instance = $this->createInstance();
		$this->callInaccessibleMethod($instance, 'evaluateLogicalOperator', [TRUE], 'foo', [FALSE]);
	}

	/**
	 * @test
	 */
	public function prepareSideForEvaluation() {
		$instance = $this->createInstance();
		$this->assertEquals([TRUE], $this->callInaccessibleMethod($instance, 'prepareSideForEvaluation', [TRUE]));
	}

	/**
	 * @test
	 */
	public function prepareSideForEvaluationArray() {
		$instance = $this->createInstance();
		$this->assertEquals([TRUE], $this->callInaccessibleMethod($instance, 'prepareSideForEvaluation', [[TRUE]]));
	}

}
