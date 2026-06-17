<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Variable\Register;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\CompileWithContentArgumentAndRenderStatic;
use FluidTYPO3\Vhs\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ### Variable\Register: Get
 *
 * ViewHelper used to read the value of a TSFE-register
 * Can be used to read names of variables which contain dynamic parts:
 *
 * ```
 * <!-- if {variableName} is "Name", outputs value of {dynamicName} -->
 * {v:variable.register.get(name: 'dynamic{variableName}')}
 * ```
 */
class GetViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'Name of register');
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $name = (string) $renderChildrenClosure();
        $request = RequestResolver::tryResolveRequestFromRenderingContext($renderingContext, false);
        if (!$request instanceof ServerRequestInterface) {
            return null;
        }
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '<')) {
            return self::getLegacyRegister($request)[$name] ?? null;
        }
        $currentRegister = self::getRegisterStackCurrent($request);
        return method_exists($currentRegister, 'get') ? $currentRegister->get($name) : null;
    }

    /**
     * @return object
     */
    private static function getRegisterStack(ServerRequestInterface $request)
    {
        $registerStack = $request->getAttribute('frontend.register.stack');
        if (!is_object($registerStack)
            || !is_a($registerStack, 'TYPO3\\CMS\\Frontend\\ContentObject\\RegisterStack')
        ) {
            throw new \RuntimeException('Unable to read frontend register without register stack.', 1774448257);
        }

        return $registerStack;
    }

    private static function getRegisterStackCurrent(ServerRequestInterface $request): object
    {
        $registerStack = self::getRegisterStack($request);
        if (!method_exists($registerStack, 'current')) {
            throw new \RuntimeException('Unable to read frontend register without current register scope.', 1774619304);
        }
        $current = $registerStack->current();
        if (!is_object($current)) {
            throw new \RuntimeException('Unable to read frontend register without current register scope.', 1774619305);
        }

        return $current;
    }

    private static function getLegacyRegister(ServerRequestInterface $request): array
    {
        $controller = $request->getAttribute('frontend.controller');
        if (!is_object($controller) || !isset($controller->register) || !is_array($controller->register)) {
            throw new \RuntimeException('Unable to read frontend register without frontend controller.', 1774619302);
        }

        return $controller->register;
    }
}
