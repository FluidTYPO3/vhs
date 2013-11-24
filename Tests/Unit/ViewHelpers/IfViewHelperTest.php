<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @protection off
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 */
class Tx_Vhs_ViewHelpers_IfViewHelperTest extends Tx_Vhs_ViewHelpers_AbstractViewHelperTest {

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
