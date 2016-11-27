<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Class RandomViewHelperTest
 */
class RandomViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param array $asArray
     */
    public function testRender(array $arguments, array $asArray)
    {
        if (true === isset($arguments['as'])) {
            $value = $this->executeViewHelperUsingTagContent($this->createObjectAccessorNode('variable'), $arguments);
        } else {
            $value = $this->executeViewHelper($arguments);
            $value2 = $this->executeViewHelperUsingTagContent($this->createObjectAccessorNode('v'), [], ['v' => $arguments['subject']]);
            if (null !== $value2) {
                $this->assertContains($value2, $asArray);
            }
        }
        if (null !== $value) {
            $this->assertContains($value, $asArray);
        } else {
            $this->assertNull($value);
        }
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        $queryResult = $this->getMockBuilder(QueryResult::class)->setMethods(['toArray', 'initialize', 'rewind', 'valid', 'count'])->disableOriginalConstructor()->getMock();
        $queryResult->expects($this->any())->method('toArray')->will($this->returnValue(['foo', 'bar']));
        $queryResult->expects($this->any())->method('count')->will($this->returnValue(0));
        $queryResult->expects($this->any())->method('valid')->will($this->returnValue(false));
        return [
            [['subject' => ['foo', 'bar']], ['foo', 'bar']],
            [['subject' => ['foo', 'bar'], 'as' => 'variable'], ['foo', 'bar']],
            [['subject' => new \ArrayIterator(['foo', 'bar'])], ['foo', 'bar']],
            [['subject' => new \ArrayIterator(['foo', 'bar']), 'as' => 'variable'], ['foo', 'bar']],
            [['subject' => $queryResult], ['foo', 'bar']],
            [['subject' => $queryResult, 'as' => 'variable'], ['foo', 'bar']]
        ];
    }
}
