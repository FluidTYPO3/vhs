<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * @protection on
 * @package Vhs
 */
class ResourceUtilityTest extends UnitTestCase
{

    /**
     * @test
     */
    public function canGetFileInformationArrayFromFileObject()
    {
        $propertiesFromFile = array('foo' => 123, 'bar' => 321);
        $propertiesFromStorage = array('foo' => 'abc', 'baz' => 123);
        $expectation = array_merge($propertiesFromFile, $propertiesFromStorage);
        $mockStorage = $this->getMock('TYPO3\CMS\Core\Resource\Storage', array('getFileInfo'));
        $mockFile = $this->getMock('TYPO3\CMS\Core\Resource\File', array('getProperties', 'getStorage', 'toArray'), array(), '', false);
        $mockFile->expects($this->once())->method('getProperties')->will($this->returnValue($propertiesFromFile));
        $mockFile->expects($this->once())->method('getStorage')->will($this->returnValue($mockStorage));
        $mockStorage->expects($this->once())->method('getFileInfo')->will($this->returnValue($propertiesFromStorage));
        $result = ResourceUtility::getFileArray($mockFile);
        $this->assertEquals($expectation, $result);
    }
}
