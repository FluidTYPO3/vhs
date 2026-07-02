<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\ImageResourceProxy;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummySourceSetViewHelper;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class SourceSetViewHelperTraitTest extends AbstractTestCase
{
    /**
     * @param array|\Traversable|string $sourceSetsArgument
     * @dataProvider getAddSourceSetTestValues
     */
    public function testAddSourceSets($sourceSetsArgument): void
    {
        $resourceMock = $this->getMockBuilder(ImageResourceProxy::class)->disableOriginalConstructor()->getMock();
        $resourceMock->method('isValid')->willReturn(true);
        $resourceMock->method('getPublicUrl')->willReturn('url');

        $tagBuilder = $this->getMockBuilder(TagBuilder::class)
            ->setMethods(['addAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagBuilder->expects(self::atLeastOnce())->method('addAttribute');

        $subject = $this->getMockBuilder(DummySourceSetViewHelper::class)
            ->onlyMethods(['getImgResource'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->method('getImgResource')->willReturn($resourceMock);
        $subject->arguments['treatIdAsReference'] = false;
        $subject->arguments['format'] = 'png';
        $subject->arguments['quality'] = 70;
        $subject->arguments['crop'] = null;
        $subject->arguments['srcset'] = $sourceSetsArgument;

        $output = $subject->addSourceSet($tagBuilder, 'source');
        self::assertNotEmpty($output);
    }

    public function getAddSourceSetTestValues(): array
    {
        return [
            'with string srcset' => ['100,200'],
            'with array srcset' => [[100, 200]],
            'with traversable srcset' => [new \ArrayIterator([100, 200])],
        ];
    }
}
