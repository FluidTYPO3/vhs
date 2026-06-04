<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Render\RecordViewHelper;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class RecordViewHelperTest
 */
class RecordViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $contentObject = $this->getMockBuilder(ContentObjectRenderer::class)
            ->onlyMethods(['cObjGetSingle'])
            ->disableOriginalConstructor()
            ->getMock();
        $contentObject->expects($this->any())->method('cObjGetSingle')->willReturnArgument(0);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())->withAttribute('currentContentObject', $contentObject);
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);
    }

    /**
     * @test
     */
    public function requiresUid(): void
    {
        $record = ['hasnouid' => 1];
        $result = $this->executeViewHelper(['record' => $record]);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function delegatesToRenderRecord(): void
    {
        $record = ['uid' => 1];
        $mock = $this->getMockBuilder(RecordViewHelper::class)
            ->onlyMethods(['renderChildren'])
            ->getMock();
        self::assertInstanceOf(RecordViewHelper::class, $mock);
        self::assertNotNull($this->renderingContext);
        $mock->setRenderingContext($this->renderingContext);
        $mock->setArguments(['record' => $record]);
        $mock->expects($this->never())->method('renderChildren');
        $result = $mock->render();
        $this->assertEquals('RECORDS', $result);
    }
}
