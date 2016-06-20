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
 * Class PopViewHelperTest
 */
class PopViewHelperTest extends AbstractViewHelperTest
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
        return array(
            array(array('subject' => array()), null),
            array(array('subject' => array('foo', 'bar')), 'bar'),
            array(array('subject' => array('foo', 'bar'), 'as' => 'variable'), 'bar'),
            array(array('subject' => new \ArrayIterator(array('foo', 'bar'))), 'bar'),
            array(array('subject' => new \ArrayIterator(array('foo', 'bar')), 'as' => 'variable'), 'bar'),
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
