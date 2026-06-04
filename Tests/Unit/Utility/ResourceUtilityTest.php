<?php
namespace FluidTYPO3\Vhs\Utility;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceStorage;

class ResourceUtilityTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function canGetFileInformationArrayFromFileObject(): void
    {
        $propertiesFromFile = ['foo' => 123, 'bar' => 321];
        $propertiesFromStorage = ['foo' => 'abc', 'baz' => 123];
        $expectation = array_merge($propertiesFromFile, $propertiesFromStorage);

        $mockStorage = $this->getMockBuilder(ResourceStorage::class)
            ->onlyMethods(['getFileInfo'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockFile = $this->getMockBuilder(File::class)
            ->onlyMethods(['getProperties', 'getStorage', 'toArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockFile->expects(self::once())->method('getProperties')->willReturn($propertiesFromFile);
        $mockFile->expects(self::once())->method('getStorage')->willReturn($mockStorage);
        $mockFile->expects(self::once())->method('toArray')->willReturn([]);
        $mockStorage->expects(self::once())->method('getFileInfo')->willReturn($propertiesFromStorage);

        $result = ResourceUtility::getFileArray($mockFile);
        self::assertSame($expectation, $result);
    }
}
