<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class TyposcriptViewHelperTest
 */
class TyposcriptViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsNullIfPathIsNull()
    {
        $this->assertNull($this->executeViewHelper(['path' => null]));
    }

    /**
     * @test
     */
    public function returnsArrayIfPathContainsArray()
    {
        $this->assertThat($this->executeViewHelper(['path' => 'config.tx_extbase.features']), new \PHPUnit_Framework_Constraint_IsType(\PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY));
    }

    /**
     * @test
     */
    public function canGetPathUsingArgument()
    {
        $this->assertNotEmpty($this->executeViewHelper(['path' => 'config.tx_extbase.features']));
    }

    /**
     * @test
     */
    public function canGetPathUsingTagContent()
    {
        $this->assertNotEmpty($this->executeViewHelperUsingTagContent('config.tx_extbase.features'));
    }
}
