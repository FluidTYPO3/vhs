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
 * Class FirstViewHelperTest
 */
class FirstViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsFirstElement()
    {
        $array = array('a', 'b', 'c');
        $arguments = array(
            'haystack' => $array
        );
        $output = $this->executeViewHelper($arguments);
        $this->assertEquals('a', $output);
    }

    /**
     * @test
     */
    public function supportsIterators()
    {
        $array = new \ArrayIterator(array('a', 'b', 'c'));
        $arguments = array(
            'haystack' => $array
        );
        $output = $this->executeViewHelper($arguments);
        $this->assertEquals('a', $output);
    }

    /**
     * @test
     */
    public function supportsTagContent()
    {
        $array = array('a', 'b', 'c');
        $arguments = array(
            'haystack' => null
        );
        $output = $this->executeViewHelperUsingTagContent('Array', $array, $arguments);
        $this->assertEquals('a', $output);
    }

    /**
     * @test
     */
    public function returnsNullIfHaystackIsNull()
    {
        $arguments = array(
            'haystack' => null
        );
        $output = $this->executeViewHelper($arguments);
        $this->assertEquals(null, $output);
    }

    /**
     * @test
     */
    public function returnsNullIfHaystackIsEmptyArray()
    {
        $arguments = array(
            'haystack' => array()
        );
        $output = $this->executeViewHelper($arguments);
        $this->assertEquals(null, $output);
    }

    /**
     * @test
     */
    public function throwsExceptionOnUnsupportedHaystacks()
    {
        $arguments = array(
            'haystack' => new \DateTime('now')
        );
        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'Invalid argument supplied to Iterator/FirstViewHelper - expected array, Iterator or NULL but got');
        $this->executeViewHelper($arguments);
    }
}
