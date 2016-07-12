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
 * Class SortViewHelperTest
 */
class SortViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function throwsExceptionOnUnsupportedSortFlag()
    {
        $arguments = array('sortFlags' => 'FOOBAR');
        $this->setExpectedException('TYPO3\CMS\Fluid\Core\ViewHelper\Exception', 'The constant "FOOBAR" you\'re trying to use as a sortFlag is not allowed.');
        $this->executeViewHelperUsingTagContent('Array', array('a', 'b', 'c'), $arguments);
    }
}
