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
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$GLOBALS['TYPO3_DB'] = $this->getMock(
			'TYPO3\\CMS\\Core\\Database\\DatabaseConnection',
			['exec_SELECTquery', 'sql_fetch_assoc'],
			[], '', FALSE
		);
		$GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTquery')->willReturn(NULL);
	}

	/**
	 * @test
	 */
	public function generatesPageLinks() {
		$arguments = ['pageUid' => 1];
		$GLOBALS['TYPO3_DB']->expects($this->any())->method('sql_fetch_assoc')->willReturn(['uid' => '1', 'title' => 'test']);
		$result = $this->executeViewHelper($arguments, [], NULL, 'Vhs');
		$this->assertNotEmpty($result);
	}

	/**
	 * @test
	 */
	public function generatesNullLinkOnZeroPageUid() {
		$arguments = ['pageUid' => 0];
		$GLOBALS['TYPO3_DB']->expects($this->any())->method('sql_fetch_assoc')->willReturn(FALSE);
		$result = $this->executeViewHelper($arguments, [], NULL, 'Vhs');
		$this->assertNull($result);
	}

	/**
	 * @test
	 */
	public function generatesPageLinksWithCustomTitle() {
		$arguments = ['pageUid' => 1, 'pageTitleAs' => 'title'];
		$GLOBALS['TYPO3_DB']->expects($this->any())->method('sql_fetch_assoc')->willReturn(['uid' => '1', 'title' => 'test']);
		$result = $this->executeViewHelperUsingTagContent('Text', 'customtitle', $arguments, [], 'Vhs');
		$this->assertContains('customtitle', $result);
	}

	/**
	 * @test
	 */
	public function generatesPageWizardLinks() {
		$arguments = ['pageUid' => '1 2 3 4 5 foo=bar&baz=123'];
		$GLOBALS['TYPO3_DB']->expects($this->any())->method('sql_fetch_assoc')->willReturn(['uid' => '1', 'title' => 'test']);
		$result = $this->executeViewHelper($arguments, [], NULL, 'Vhs');
		$this->assertNotEmpty($result);
	}

}
