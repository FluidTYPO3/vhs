<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Context;

/***************************************************************
 *  Copyright notice
 *  (c) 2014 Benjamin Beck <beck@beckdigitalemedien.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest;

/**
 * @author     Benjamin Beck <beck@beckdigitalemedien.de>
 * @package    Vhs
 */
class GetViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function renderCompleteContext() {
		$result = $this->executeViewHelper();
		$expectedResult = getenv('TYPO3_CONTEXT') ?: (getenv('REDIRECT_TYPO3_CONTEXT') ?: 'Production');
		$this->assertSame($result, $expectedResult);
	}

}
