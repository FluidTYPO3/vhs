<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format\Json;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\ViewHelpers\Format\Json\DecodeViewHelper;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/**
 * Class DecodeViewHelperTest
 */
class DecodeViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function returnsNullForEmptyArguments()
    {
        $result = DecodeViewHelper::renderStatic([], function () {}, $this->objectManager->get(RenderingContext::class));
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function returnsExpectedValueForProvidedArguments()
    {

        $fixture = '{"foo":"bar","bar":true,"baz":1,"foobar":null}';

        $expected = [
            'foo' => 'bar',
            'bar' => true,
            'baz' => 1,
            'foobar' => null,
        ];

        $result = $this->executeViewHelper(['json' => $fixture]);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function throwsExceptionForInvalidArgument()
    {
        $invalidJson = "{'foo': 'bar'}";
        $this->expectViewHelperException();
        $this->executeViewHelper(['json' => $invalidJson]);
    }
}
