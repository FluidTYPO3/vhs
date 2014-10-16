<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Cedric Ziel <cedric@cedric-ziel.com>
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

use FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection on
 * @package Vhs
 */
class LinkViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function generatesPageLinks() {
		$arguments = array('pageUid' => 1);
		$result = $this->executeViewHelper($arguments);
		$this->assertNotEmpty($result);
	}

	/**
	 * @test
	 */
	public function generatesNullLinkOnZeroPageUid() {
		$arguments = array('pageUid' => 0);
		$result = $this->executeViewHelper($arguments);
		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function generatesPageLinksWithCustomTitle() {
		$arguments = array('pageUid' => 1, 'pageTitleAs' => 'title');
		$result = $this->executeViewHelperUsingTagContent('Text', 'customtitle', $arguments);
		$this->assertContains('customtitle', $result);
	}

	/**
	 * @test
	 */
	public function generatesPageWizardLinks() {
		$arguments = array('pageUid' => '1 2 3 4 5 foo=bar&baz=123');
		$result = $this->executeViewHelper($arguments);
		$this->assertNotEmpty($result);
	}

}
