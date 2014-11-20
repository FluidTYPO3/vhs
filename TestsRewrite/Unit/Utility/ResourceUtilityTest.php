<?php
namespace FluidTYPO3\Vhs\Utility;
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
 ***************************************************************/

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * @protection on
 * @package Vhs
 */
class ResourceUtilityTest extends UnitTestCase {

	/**
	 * @test
	 */
	public function canGetFileInformationArrayFromFileObject() {
		$propertiesFromFile = array('foo' => 123, 'bar' => 321);
		$propertiesFromStorage = array('foo' => 'abc', 'baz' => 123);
		$expectation = array_merge($propertiesFromFile, $propertiesFromStorage);
		$mockStorage = $this->getMock('TYPO3\CMS\Core\Resource\Storage', array('getFileInfo'));
		$mockFile = $this->getMock('TYPO3\CMS\Core\Resource\File', array('getProperties', 'getStorage', 'toArray'), array(), '', FALSE);
		$mockFile->expects($this->once())->method('getProperties')->will($this->returnValue($propertiesFromFile));
		$mockFile->expects($this->once())->method('getStorage')->will($this->returnValue($mockStorage));
		$mockStorage->expects($this->once())->method('getFileInfo')->will($this->returnValue($propertiesFromStorage));
		$result = ResourceUtility::getFileArray($mockFile);
		$this->assertEquals($expectation, $result);
	}

}
