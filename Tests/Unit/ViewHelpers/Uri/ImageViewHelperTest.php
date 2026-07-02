<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\Utility\VersionUtility;
use TYPO3\CMS\Core\TypoScript\AST\Node\RootNode;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;

/**
 * Class ImageViewHelperTest
 */
class ImageViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        if (VersionUtility::isCoreAtLeast14()) {
            $this->frontendTypoScript = new FrontendTypoScript(new RootNode(), [], [], []);
            $this->frontendTypoScript->setSetupArray(['config.' => ['absRefPrefix' => 'a', 'forceAbsoluteUrls' => 1]]);
        }

        parent::setUp();
    }

    /**
     * @test
     */
    public function callsExpectedMethodSequence()
    {
        $mock = $this->getMockBuilder($this->getViewHelperClassName())->setMethods(['preprocessImage'])->getMock();
        $arguments = $this->buildViewHelperArguments($mock, ['src' => 'foobar']);
        $mock->setArguments($arguments);
        $output = $mock->render();
        $this->assertSame('', $output);
    }
}
