<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Content\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;

/**
 * Class FalViewHelperTest
 */
class FalViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $this->singletonInstances[ResourceFactory::class] = $this->getMockBuilder(ResourceFactory::class)->disableOriginalConstructor()->getMock();
        $this->singletonInstances[FileRepository::class] = $this->getMockBuilder(FileRepository::class)->disableOriginalConstructor()->getMock();

        parent::setUp();
    }

    public function testRender()
    {
        $this->markTestSkipped('Test skipped pending refactoring to Doctrine QueryBuilder');

        $GLOBALS['TYPO3_DB'] = $this->getMockBuilder(DatabaseConnection::class)->setMethods(['fullQuoteStr', 'exec_SELECTquery', 'sql_fetch_assoc'])->disableOriginalConstructor()->getMock();
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('fullQuoteStr')->willReturnArgument(0);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTquery')->willReturn(null);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('sql_fetch_assoc')->willReturn([]);
        $this->assertEmpty($this->executeViewHelper());
    }
}
