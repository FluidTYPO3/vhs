<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\String;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class IsUppercaseViewHelperTest
 */
class IsUppercaseViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function rendersThenChildIfFirstCharacterIsUppercase()
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'Foobar',
            'fullString' => false
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersThenChildIfAllCharactersAreUppercase()
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'FOOBAR',
            'fullString' => true
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersElseChildIfFirstCharacterIsNotUppercase()
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'fooBar',
            'fullString' => false
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }

    /**
     * @test
     */
    public function rendersElseChildIfAllCharactersAreNotUppercase()
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'FooBar',
            'fullString' => true
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
