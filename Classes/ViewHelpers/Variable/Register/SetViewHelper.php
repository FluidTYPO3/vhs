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
 * ### Variable\Register: Set
 *
 * Sets a single register in the TSFE-register.
 *
 * Using as `{value -> v:variable.register.set(name: 'myVar')}` makes $GLOBALS["TSFE"]->register['myVar']
 * contain `{value}`.
 */
class SetViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'mixed', 'Value to set');
        $this->registerArgument('name', 'string', 'Name of register', true);
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $name = $arguments['name'];
        if (!is_string($name) || $name === '') {
            throw new \RuntimeException('Unable to set frontend register without register name.', 1774448258);
        }

        $value = $renderChildrenClosure();
        if (!is_string($value) && !is_int($value) && !is_bool($value) && !is_float($value)) {
            throw new \RuntimeException('Frontend register values must be scalar.', 1774448259);
        }
        $request = RequestResolver::tryResolveRequestFromRenderingContext($renderingContext, false);
        if (!$request instanceof ServerRequestInterface) {
            return null;
        }
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14.0', '<')) {
            self::setLegacyRegister($request, $name, $value);
            return null;
        }
        $currentRegister = self::getRegisterStackCurrent($request);
        if (!method_exists($currentRegister, 'set')) {
            throw new \RuntimeException(
                'Unable to write frontend register without writable register scope.',
                1774619306
            );
        }
        $currentRegister->set($name, $value);
        return null;
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
            throw new \RuntimeException('Unable to write frontend register without register stack.', 1774448261);
        }

        return $registerStack;
    }

    private static function getRegisterStackCurrent(ServerRequestInterface $request): object
    {
        $registerStack = self::getRegisterStack($request);
        if (!method_exists($registerStack, 'current')) {
            throw new \RuntimeException(
                'Unable to write frontend register without current register scope.',
                1774619307
            );
        }
        $current = $registerStack->current();
        if (!is_object($current)) {
            throw new \RuntimeException(
                'Unable to write frontend register without current register scope.',
                1774619308
            );
        }

        return $current;
    }

    private static function setLegacyRegister(ServerRequestInterface $request, string $name, mixed $value): void
    {
        $controller = $request->getAttribute('frontend.controller');
        if (!is_object($controller) || !isset($controller->register) || !is_array($controller->register)) {
            throw new \RuntimeException('Unable to write frontend register without frontend controller.', 1774619303);
        }

        $controller->register[$name] = $value;
    }
}
