<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Type;
use PHPUnit\Framework\Attributes\Test;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

/**
 * Class IsDomainObjectViewHelperTest
 */
class IsDomainObjectViewHelperTest extends AbstractViewHelperTestCase
{
    #[Test]
    public function rendersThenChildIfConditionMatched(): void
    {
        if (!class_exists(FrontendUser::class)) {
            self::markTestSkipped('Skipping test with FrontendUser dependency');
        }
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'value' => new FrontendUser()
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('then', $result);
    }

    #[Test]
    public function rendersElseChildIfConditionNotMatched(): void
    {
        $arguments = [
            'then' => 'then',
            'else' => 'else',
            'value' => new \stdClass()
        ];
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);
    }
}
