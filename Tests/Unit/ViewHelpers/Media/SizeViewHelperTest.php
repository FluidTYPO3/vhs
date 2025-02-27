<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\ResourceFactoryProxy;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;

/**
 * Class SizeViewHelperTest
 */
class SizeViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @var string
     */
    protected $fixturesPath;

    /**
     * Setup
     */
    public function setUp(): void
    {
        $this->singletonInstances[ResourceFactoryProxy::class] = $this->getMockBuilder(ResourceFactoryProxy::class)->disableOriginalConstructor()->getMock();
        parent::setUp();
        $this->fixturesPath = realpath(__DIR__ . '/../../../../Tests/Fixtures/Files');
    }

    /**
     * @test
     */
    public function returnsZeroForEmptyArguments()
    {
        $this->assertEquals(0, $this->executeViewHelper());
    }

    /**
     * @test
     */
    public function returnsFileSizeAsInteger()
    {
        $this->assertEquals(7094, $this->executeViewHelperUsingTagContent($this->fixturesPath . '/typo3_logo.jpg'));
    }

    /**
     * @test
     */
    public function throwsExceptionWhenFileNotFound()
    {
        $this->expectViewHelperException();
        $this->executeViewHelperUsingTagContent('/this/path/hopefully/does/not/exist.txt');
    }

    /**
     * @test
     */
    public function throwsExceptionWhenFileIsNotAccessibleOrIsADirectory()
    {
        $this->expectViewHelperException();
        $this->executeViewHelperUsingTagContent($this->fixturesPath);
    }
}
