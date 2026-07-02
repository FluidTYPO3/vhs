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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class RecordViewHelperTest
 */
class RecordViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $cObj = $this->getMockBuilder(ContentObjectRenderer::class)
            ->onlyMethods(['cObjGetSingle'])
            ->disableOriginalConstructor()
            ->getMock();
        $cObj->method('cObjGetSingle')->willReturn('RECORDS');
        $this->simulateRequestWithExtbaseParameters('Vhs', 123, $cObj);
    }

    /**
     * @test
     */
    public function requiresUid()
    {
        $record = ['hasnouid' => 1];
        $result = $this->executeViewHelper(['record' => $record]);
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function delegatesToRenderRecord()
    {
        $record = ['uid' => 1];
        $mock = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['renderChildren'])->getMock();
        $mock->setRenderingContext($this->renderingContext);
        $mock->setArguments(['record' => $record]);
        $mock->expects($this->never())->method('renderChildren');
        $result = $mock->render();
        $this->assertEquals('RECORDS', $result);
    }
}
