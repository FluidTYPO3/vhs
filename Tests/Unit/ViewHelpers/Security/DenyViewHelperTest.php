<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Security;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class DenyViewHelperTest extends AbstractViewHelperTest {

	public function testConditionalRenderTrue() {
		$instance = $this->getMock($this->getViewHelperClassName(), array('evaluateArguments', 'renderThenChild', 'renderElseChild'));
		$instance->expects($this->once())->method('evaluateArguments')->willReturn(FALSE);
		$instance->expects($this->once())->method('renderThenChild');
		$instance->expects($this->never())->method('renderElseChild');
		$instance->render();
	}

	public function testConditionalRenderFalse() {
		$instance = $this->getMock($this->getViewHelperClassName(), array('evaluateArguments', 'renderThenChild', 'renderElseChild'));
		$instance->expects($this->once())->method('evaluateArguments')->willReturn(TRUE);
		$instance->expects($this->never())->method('renderThenChild');
		$instance->expects($this->once())->method('renderElseChild');
		$instance->render();
	}

}
