<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Development\AbstractTestCase;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceStorage;

/**
 * @protection on
 * @package Vhs
 */
class ResourceUtilityTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function canGetFileInformationArrayFromFileObject()
    {
        $propertiesFromFile = ['foo' => 123, 'bar' => 321];
        $propertiesFromStorage = ['foo' => 'abc', 'baz' => 123];
        $expectation = array_merge($propertiesFromFile, $propertiesFromStorage);
        $mockStorage = $this->getMockBuilder(ResourceStorage::class)->setMethods(['getFileInfo'])->disableOriginalConstructor()->getMock();
        $mockFile = $this->getMockBuilder(File::class)->setMethods(['getProperties', 'getStorage', 'toArray'])->disableOriginalConstructor()->getMock();
        $mockFile->expects($this->once())->method('getProperties')->will($this->returnValue($propertiesFromFile));
        $mockFile->expects($this->once())->method('getStorage')->will($this->returnValue($mockStorage));
        $mockStorage->expects($this->once())->method('getFileInfo')->will($this->returnValue($propertiesFromStorage));
        $result = ResourceUtility::getFileArray($mockFile);
        $this->assertEquals($expectation, $result);
    }
}
