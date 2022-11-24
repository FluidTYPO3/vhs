<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Type;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Class IsQueryResultViewHelperTest
 */
class IsQueryResultViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function rendersThenChildIfConditionMatched()
    {
        $queryResult = $this->getMockBuilder(QueryResult::class)->setMethods(['toArray', 'initialize', 'rewind', 'valid', 'count'])->disableOriginalConstructor()->getMock();
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'value' => $queryResult
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    /**
     * @test
     */
    public function rendersElseChildIfConditionNotMatched()
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'value' => 1
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }
}
