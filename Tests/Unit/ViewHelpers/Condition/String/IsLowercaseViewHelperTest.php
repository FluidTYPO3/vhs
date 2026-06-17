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
 * Class IsLowercaseViewHelperTest
 */
class IsLowercaseViewHelperTest extends AbstractViewHelperTestCase
{
    #[Test]
    public function rendersThenChildIfFirstCharacterIsLowercase(): void
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'foobar',
            'fullString' => false
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    #[Test]
    public function rendersThenChildIfAllCharactersAreLowercase(): void
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'foobar',
            'fullString' => true
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    #[Test]
    public function rendersElseChildIfFirstCharacterIsNotLowercase(): void
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'FooBar',
            'fullString' => false
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }

    #[Test]
    public function rendersElseChildIfAllCharactersAreNotLowercase(): void
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'string' => 'fooBar',
            'fullString' => true
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }
}
