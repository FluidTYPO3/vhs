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
 * Class ReverseViewHelperTest
 */
class ReverseViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $expectedValue
     */
    public function testRender(array $arguments, $expectedValue)
    {
        if (true === isset($arguments['as'])) {
            $value = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'variable', $arguments);
        } else {
            $value = $this->executeViewHelper($arguments);
            $value2 = $this->executeViewHelperUsingTagContent('ObjectAccessor', 'v', array(), array('v' => $arguments['subject']));
            $this->assertEquals($value, $value2);
        }
        $this->assertEquals($value, $expectedValue);
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
        $queryResult->expects($this->any())->method('valid')->will($this->returnValue(false));
        $queryResult->expects($this->any())->method('count')->will($this->returnValue(1));
        return array(
            array(array('subject' => array()), array()),
            array(array('subject' => array('foo', 'bar')), array(1 => 'bar', 0 => 'foo')),
            array(array('subject' => array('foo', 'bar'), 'as' => 'variable'), array(1 => 'bar', 0 => 'foo')),
            array(array('subject' => new \ArrayIterator(array('foo', 'bar'))), array(1 => 'bar', 0 => 'foo')),
            array(array('subject' => new \ArrayIterator(array('foo', 'bar')), 'as' => 'variable'), array(1 => 'bar', 0 => 'foo'))
        );
    }

    /**
     * @test
     * @dataProvider getErrorTestValues
     * @param mixed $subject
     */
    public function testThrowsErrorsOnInvalidSubjectType($subject)
    {
        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Unsupported input type; cannot convert to array!');
        $this->executeViewHelper(array('subject' => $subject));
    }

    /**
     * @return array
     */
    public function getErrorTestValues()
    {
        return array(
            array(0),
            array(null),
            array(new \DateTime()),
            array(new \stdClass()),
        );
    }
}
