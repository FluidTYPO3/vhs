<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

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
            $value = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'variable', $arguments);
        } else {
            $value = $this->executeViewHelper($arguments);
            $value2 = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', array(), array('v' => $arguments['subject']));
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
        $queryResult = $this->getMock(
            'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult',
            array('toArray', 'initialize', 'rewind', 'valid', 'count'),
            array(),
            '',
            false
        );
        $queryResult->expects($this->any())->method('toArray')->will($this->returnValue(array('foo', 'bar')));
        $queryResult->expects($this->any())->method('count')->will($this->returnValue(0));
        $queryResult->expects($this->any())->method('valid')->will($this->returnValue(false));
        return array(
            array(array('subject' => array('foo', 'bar')), array('foo', 'bar')),
            array(array('subject' => array('foo', 'bar'), 'as' => 'variable'), array('foo', 'bar')),
            array(array('subject' => new \ArrayIterator(array('foo', 'bar'))), array('foo', 'bar')),
            array(array('subject' => new \ArrayIterator(array('foo', 'bar')), 'as' => 'variable'), array('foo', 'bar')),
            array(array('subject' => $queryResult), array('foo', 'bar')),
            array(array('subject' => $queryResult, 'as' => 'variable'), array('foo', 'bar'))
        );
    }
}
