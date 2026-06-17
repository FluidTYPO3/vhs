<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Type;
use PHPUnit\Framework\Attributes\Test;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Class IsQueryResultViewHelperTest
 */
class IsQueryResultViewHelperTest extends AbstractViewHelperTestCase
{
    #[Test]
    public function rendersThenChildIfConditionMatched(): void
    {
        $queryResult = $this->getMockBuilder(QueryResult::class)
            ->onlyMethods(['toArray', 'initialize', 'rewind', 'valid', 'count'])
            ->disableOriginalConstructor()
            ->getMock();
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'value' => $queryResult
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    #[Test]
    public function rendersElseChildIfConditionNotMatched(): void
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
