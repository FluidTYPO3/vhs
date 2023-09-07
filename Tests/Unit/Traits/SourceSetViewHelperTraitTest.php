<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummySourceSetViewHelper;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class SourceSetViewHelperTraitTest extends AbstractTestCase
{
    /**
     * @param array|\Traversable|string $sourceSetsArgument
     * @dataProvider getAddSourceSetTestValues
     */
    public function testAddSourceSets($sourceSetsArgument): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST']->method('getAttribute')->willReturn(SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $tagBuilder = $this->getMockBuilder(TagBuilder::class)
            ->setMethods(['addAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $tagBuilder->expects(self::atLeastOnce())->method('addAttribute');

        $subject = new DummySourceSetViewHelper();
        $subject->arguments['treatIdAsReference'] = false;
        $subject->arguments['format'] = 'png';
        $subject->arguments['quality'] = 70;
        $subject->arguments['crop'] = null;
        $subject->arguments['srcset'] = $sourceSetsArgument;

        $subject->contentObject = $this->getMockBuilder(ContentObjectRenderer::class)
            ->setMethods(['getImgResource'])
            ->disableOriginalConstructor()
            ->getMock();
        $subject->contentObject->expects(self::atLeastOnce())
            ->method('getImgResource')
            ->willReturn(
                [
                    'name',
                    100,
                    200,
                    'path',
                ]
            );

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
