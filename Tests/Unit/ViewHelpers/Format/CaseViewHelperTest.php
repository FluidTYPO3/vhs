<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\ViewHelpers\Format\CaseViewHelper;

/**
 * Class CaseViewHelperTest
 */
class CaseViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getInputsAndExpectedOutputs
     * @param string $input
     * @param string $case
     * @param string $expectedOutput
     */
    public function convertsToExpectedFormat($input, $case, $expectedOutput)
    {
        $result = $this->executeViewHelper(['string' => $input, 'case' => $case]);
        $this->assertEquals($expectedOutput, $result);
    }

    /**
     * @return array
     */
    public function getInputsAndExpectedOutputs()
    {
        return [
            /*
			array('lowerstring', CaseViewHelper::CASE_UPPER, 'LOWERSTRING'),
			array('UPPERSTRING', CaseViewHelper::CASE_LOWER, 'upperstring'),
			array('lowerstring', CaseViewHelper::CASE_UCFIRST, 'Lowerstring'),
			array('UPPERSTRING', CaseViewHelper::CASE_LCFIRST, 'uPPERSTRING'),
			*/
            ['lots of words', CaseViewHelper::CASE_UCWORDS, 'Lots Of Words'],
            ['lowercase_underscored', CaseViewHelper::CASE_CAMELCASE, 'LowercaseUnderscored'],
            ['lowercase_underscored', CaseViewHelper::CASE_LOWERCAMELCASE, 'lowercaseUnderscored'],
            ['CamelCase', CaseViewHelper::CASE_UNDERSCORED, 'camel_case'],
            ['unknown format MIXED WITH All Cases', 'unsupported', 'unknown format MIXED WITH All Cases']
        ];
    }
}
