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

class SetViewHelperTest extends AbstractViewHelperTestCase
{
    private object $registerStorage;

    protected function setUp(): void
    {
        parent::setUp();

        [$request, $this->registerStorage] = $this->createRequestAndRegisterStorage();
        $GLOBALS['TYPO3_REQUEST'] = $request;
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);
    }

    /**
     * @test
     */
    public function throwsExceptionWithoutRegisterStack(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = new ServerRequest();
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);
        $this->expectException(\RuntimeException::class);

        $this->executeViewHelper(['name' => 'name', 'value' => 'value']);
    }

    /**
     * @test
     */
    public function returnsNullWithoutRequest(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        $this->renderingContext = $this->createRenderingContextWithoutRequest();

        self::assertNull($this->executeViewHelper(['name' => 'name', 'value' => 'value']));
    }

    /**
     * @test
     */
    public function canSetRegister(): void
    {
        $name = uniqid();
        $value = uniqid();
        $this->executeViewHelper(['name' => $name, 'value' => $value]);
        $this->assertEquals($value, $this->getRegisterValue($this->registerStorage, $name));
    }

    /**
     * @test
     */
    public function canSetVariableWithValueFromTagContent(): void
    {
        $name = uniqid();
        $value = uniqid();
        $this->executeViewHelperUsingTagContent($value, ['name' => $name]);
        $this->assertEquals($value, $this->getRegisterValue($this->registerStorage, $name));
    }

    /**
     * @test
     */
    public function writesRegisterStackFromRenderingContextRequest(): void
    {
        $name = uniqid();
        [$globalRequest, $globalRegisterStorage] = $this->createRequestAndRegisterStorage();
        [$subRequest, $subRequestRegisterStorage] = $this->createRequestAndRegisterStorage();
        $GLOBALS['TYPO3_REQUEST'] = $globalRequest;
        $this->renderingContext = $this->createRenderingContextWithRequest($subRequest);

        $this->executeViewHelper(['name' => $name, 'value' => 'inner']);

        self::assertNull($this->getRegisterValue($globalRegisterStorage, $name));
        self::assertSame('inner', $this->getRegisterValue($subRequestRegisterStorage, $name));
    }

    /**
     * @return array{0: ServerRequest, 1: object}
     */
    private function createRequestAndRegisterStorage(): array
    {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '>=')) {
            $registerStackClassName = 'TYPO3\\CMS\\Frontend\\ContentObject\\RegisterStack';
            $registerStack = new $registerStackClassName();
            return [(new ServerRequest())->withAttribute('frontend.register.stack', $registerStack), $registerStack];
        }

        $controller = new class () {
            public array $register = [];
        };
        return [(new ServerRequest())->withAttribute('frontend.controller', $controller), $controller];
    }

    private function getRegisterValue(object $registerStorage, string $name): mixed
    {
        if (method_exists($registerStorage, 'current')) {
            $current = $registerStorage->current();
            return is_object($current) && method_exists($current, 'get') ? $current->get($name) : null;
        }

        return $registerStorage->register[$name] ?? null;
    }
}
