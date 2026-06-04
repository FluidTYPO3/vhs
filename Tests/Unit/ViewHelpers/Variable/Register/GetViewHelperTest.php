<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable\Register;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Class GetViewHelperTest
 */
class GetViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @test
     */
    public function throwsExceptionWithoutRegisterStack(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = new ServerRequest();
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);
        $this->expectException(\RuntimeException::class);

        $this->executeViewHelper(['name' => 'name']);
    }

    /**
     * @test
     */
    public function returnsNullWithoutRequest(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $this->renderingContext = $this->createRenderingContextWithoutRequest();

        self::assertNull($this->executeViewHelper(['name' => 'missing']));
    }

    /**
     * @test
     */
    public function returnsNullIfRegisterDoesNotExist(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRegister();
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);
        $name = uniqid();
        $this->assertEquals(null, $this->executeViewHelper(['name' => $name]));
    }

    /**
     * @test
     */
    public function returnsValueIfRegisterExists(): void
    {
        $name = uniqid();
        $value = uniqid();
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRegister([$name => $value]);
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);
        $this->assertEquals($value, $this->executeViewHelper(['name' => $name]));
    }

    /**
     * @test
     */
    public function readsRegisterStackFromRenderingContextRequest(): void
    {
        $name = uniqid();
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRegister([$name => 'outer']);
        $this->renderingContext = $this->createRenderingContextWithRequest($this->createRequestWithRegister([
            $name => 'inner',
        ]));

        self::assertSame('inner', $this->executeViewHelper(['name' => $name]));
    }

    private function createRequestWithRegister(array $values = []): ServerRequest
    {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '>=')) {
            $registerStackClassName = 'TYPO3\\CMS\\Frontend\\ContentObject\\RegisterStack';
            $registerStack = new $registerStackClassName();
            foreach ($values as $name => $value) {
                $current = method_exists($registerStack, 'current') ? $registerStack->current() : null;
                if (is_object($current) && method_exists($current, 'set')) {
                    $current->set($name, $value);
                }
            }
            return (new ServerRequest())->withAttribute('frontend.register.stack', $registerStack);
        }

        $controller = new class () {
            public array $register = [];
        };
        $controller->register = $values;
        return (new ServerRequest())->withAttribute('frontend.controller', $controller);
    }
}
