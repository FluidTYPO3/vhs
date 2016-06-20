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
 * Class FilterViewHelperTest
 */
class FilterViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function nullSubjectCallsRenderChildrenToReadValue()
    {
        $subject = array('test' => 'test');
        $arguments = array(
            'preserveKeys' => true
        );
        $result = $this->executeViewHelperUsingTagContent('Array', $subject, $arguments);
        $this->assertSame($subject, $result);
    }

    /**
     * @test
     */
    public function filteringEmptySubjectReturnsEmptyArrayOnInvalidSubject()
    {
        $arguments = array(
            'subject' => new \DateTime('now')
        );
        $result = $this->executeViewHelper($arguments);
        $this->assertSame($result, array());
    }

    /**
     * @test
     */
    public function supportsIterators()
    {
        $array = array('test' => 'test');
        $iterator = new \ArrayIterator($array);
        $arguments = array(
            'subject' => $iterator,
            'filter' => 'test',
            'preserveKeys' => true
        );
        $result = $this->executeViewHelper($arguments);
        $this->assertSame($result, $array);
    }

    /**
     * @test
     */
    public function supportsPropertyName()
    {
        $array = array(array('test' => 'test'));
        $iterator = new \ArrayIterator($array);
        $arguments = array(
            'subject' => $iterator,
            'filter' => 'test',
            'propertyName' => 'test',
            'preserveKeys' => true
        );
        $result = $this->executeViewHelper($arguments);
        $this->assertSame($result, $array);
    }
}
