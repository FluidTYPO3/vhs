<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\String;
use PHPUnit\Framework\Attributes\Test;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class IsUppercaseViewHelperTest
 */
class IsUppercaseViewHelperTest extends AbstractViewHelperTestCase
{
    #[Test]
    public function rendersThenChildIfFirstCharacterIsUppercase(): void
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'Foobar',
            'fullString' => false
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    #[Test]
    public function rendersThenChildIfAllCharactersAreUppercase(): void
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'FOOBAR',
            'fullString' => true
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    #[Test]
    public function rendersElseChildIfFirstCharacterIsNotUppercase(): void
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'fooBar',
            'fullString' => false
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }

    #[Test]
    public function rendersElseChildIfAllCharactersAreNotUppercase(): void
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'FooBar',
            'fullString' => true
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }
}
