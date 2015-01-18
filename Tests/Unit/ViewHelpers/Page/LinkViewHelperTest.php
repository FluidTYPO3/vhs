<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection on
 * @package Vhs
 */
class LinkViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @disabledtest
	 */
	public function generatesPageLinks() {
		$arguments = array('pageUid' => 1);
		$result = $this->executeViewHelper($arguments);
		$this->assertNotEmpty($result);
	}

	/**
	 * @disabledtest
	 */
	public function generatesNullLinkOnZeroPageUid() {
		$arguments = array('pageUid' => 0);
		$result = $this->executeViewHelper($arguments);
		$this->assertNull($result);
	}

	/**
	 * @disabledtest
	 */
	public function generatesPageLinksWithCustomTitle() {
		$arguments = array('pageUid' => 1, 'pageTitleAs' => 'title');
		$result = $this->executeViewHelperUsingTagContent('Text', 'customtitle', $arguments);
		$this->assertContains('customtitle', $result);
	}

	/**
	 * @disabledtest
	 */
	public function generatesPageWizardLinks() {
		$arguments = array('pageUid' => '1 2 3 4 5 foo=bar&baz=123');
		$result = $this->executeViewHelper($arguments);
		$this->assertNotEmpty($result);
	}

}
