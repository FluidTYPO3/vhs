<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummySlideViewHelperTraitViewHelper;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;

class SlideViewHelperTraitTest extends AbstractTestCase
{
    /**
     * @dataProvider getGetSlideRecordsTestValues
     */
    public function testGetSlideRecords(
        int $expectedSize,
        array $rootLine,
        array $recordReturns,
        ?int $limit,
        int $slide,
        bool $reverse,
        int $slideCollect
    ): void {
        $pageService = $this->getMockBuilder(PageService::class)
            ->setMethods(['getRootLine'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getRootLine')->willReturn($rootLine);

        $subject = $this->getMockBuilder(DummySlideViewHelperTraitViewHelper::class)
            ->setMethods(['getPageService', 'getSlideRecordsFromPage'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getPageService')->willReturn($pageService);
        $subject->method('getSlideRecordsFromPage')->willReturnOnConsecutiveCalls($recordReturns);
        $subject->arguments['limit'] = $limit;
        $subject->arguments['slide'] = $slide;
        $subject->arguments['slideCollect'] = $slideCollect;
        $subject->arguments['slideCollectReverse'] = $reverse;

        $subject->initializeArguments();

        $output = $this->callInaccessibleMethod($subject, 'getSlideRecords', 1, $limit);
        self::assertSame($expectedSize, count($output));
    }

    public function getGetSlideRecordsTestValues(): array
    {
        return [
            'with empty root line and no records found' => [0, [], [], null, 10, false, 10],
            'with single item root line and no records found' => [0, [['uid' => 1]], [], null, 10, false, 10],
            'with single item root line (reversed) and no records found' => [0, [['uid' => 1]], [], null, 10, true, 10],
            'with single item root line and one record found' => [1, [['uid' => 1]], [['uid' => 2]], null, 10, false, 10],
            'with single item root line and one record found without slide' => [1, [['uid' => 1]], [['uid' => 2]], null, 0, false, 0],
            'with single item root line and one record found without slide but with slideCollect' => [1, [['uid' => 1]], [['uid' => 2]], null, 0, false, 10],
            'with number of records exceeding limit' => [1, [['uid' => 1], ['uid' => 2]], [['uid' => 3], ['uid' => 4]], 1, 10, false, 10],
            'with multiple root line items and without slideCollect' => [1, [['uid' => 1], ['uid' => 2]], [['uid' => 3]], 1, 10, false, 0],
        ];
    }
}
