<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format\Placeholder;

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
