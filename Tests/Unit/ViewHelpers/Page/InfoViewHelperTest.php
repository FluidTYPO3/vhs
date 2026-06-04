<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;

/**
 * Class InfoViewHelperTest
 */
class InfoViewHelperTest extends AbstractViewHelperTestCase
{
    private PageRepository&MockObject $pageRepository;

    protected function setUp(): void
    {
        $this->pageRepository = $this->getMockBuilder(PageRepository::class)
            ->onlyMethods(['getPage_noCheck'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService = $this->getMockBuilder(PageService::class)
            ->onlyMethods(['getPageRepository'])
            ->disableOriginalConstructor()
            ->getMock();
        $pageService->method('getPageRepository')->willReturn($this->pageRepository);

        $this->singletonInstances[PageService::class] = $pageService;

        parent::setUp();
    }

    public function testUsesPageUidFromRequestRouting(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute(
            'routing',
            new PageArguments(123, '0', [])
        );
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);
        $this->pageRepository->expects(self::once())->method('getPage_noCheck')->with(123);
        $this->executeViewHelper(['pageUid' => 0, 'field' => 'tx_foo_bar']);
    }

    public function testReturnsCorrectSingleFieldValue(): void
    {
        $expectedFieldValue = 42;

        $this->pageRepository->expects($this->any())
            ->method('getPage_noCheck')
            ->willReturn(['tx_foo_bar' => $expectedFieldValue]);
        $this->assertEquals($expectedFieldValue, $this->executeViewHelper(['pageUid' => 12, 'field' => 'tx_foo_bar']));
    }

    public function testReturnsPageRowIfNoFieldGiven(): void
    {
        $expectedRow = ['uid' => 42, 'tx_foo_bar' => 'baz'];

        $this->pageRepository->expects($this->any())
            ->method('getPage_noCheck')
            ->willReturn($expectedRow);
        $this->assertEquals($expectedRow, $this->executeViewHelper(['pageUid' => 42]));
    }
}
