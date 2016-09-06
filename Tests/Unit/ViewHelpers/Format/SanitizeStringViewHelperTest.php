<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class SanitizeStringViewHelperTest
 */
class SanitizeStringViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getInputsAndExpectedOutputs
     * @param string $input
     * @param string $expectedOutput
     */
    public function sanitizesString($input, $expectedOutput)
    {
        $result1 = $this->executeViewHelper(array('string' => $input));
        $result2 = $this->executeViewHelperUsingTagContent('Text', $input);
        $this->assertEquals($expectedOutput, $result1);
        $this->assertEquals($result1, $result2);
    }

    /**
     * @return array
     */
    public function getInputsAndExpectedOutputs()
    {
        return array(
            array('this string needs dashes', 'this-string-needs-dashes'),
            array('THIS SHOULD BE LOWERCASE', 'this-should-be-lowercase'),
            array('THESE øæå chars are not allowed', 'these-oeaeaa-chars-are-not-allowed'),
            array('many           spaces become one dash', 'many-spaces-become-one-dash')
        );
    }
}
