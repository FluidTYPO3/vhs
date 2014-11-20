<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Placeholder;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
 * ************************************************************* */

use FluidTYPO3\Vhs\ViewHelpers\AbstractViewHelperTest;

/**
 * @protection on
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class ImageViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @var array
	 */
	protected $arguments = array(
		'width' => 100,
		'height' => 100,
		'backgroundColor' => '333333',
		'textColor' => 'FFFFFF'
	);

	/**
	 * @test
	 */
	public function rendersImage() {
		$arguments = $this->arguments;
		$test = $this->executeViewHelper($arguments);
		$this->assertSame('<img src="http://placehold.it/100/333333/FFFFFF" alt="http://placehold.it/100/333333/FFFFFF" width="100" height="100" />', $test);
	}

	/**
	 * @test
	 */
	public function rendersImageWithText() {
		$arguments = $this->arguments;
		$arguments['text'] = 'test';
		$test = $this->executeViewHelper($arguments);
		$this->assertSame('<img src="http://placehold.it/100/333333/FFFFFF/&amp;text=test" alt="http://placehold.it/100/333333/FFFFFF/&amp;text=test" width="100" height="100" />', $test);
	}

}
