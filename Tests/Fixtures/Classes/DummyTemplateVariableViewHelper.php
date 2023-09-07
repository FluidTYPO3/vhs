<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;

class DummyTemplateVariableViewHelper
{
    use TemplateVariableViewHelperTrait;

    public array $arguments = [
        'as' => null,
    ];

    /**
     * Current variable container reference.
     * @var VariableProviderInterface
     * @api
     */
    public $templateVariableContainer;

    private function registerArgument(
        string $name,
        string $type,
        string $description,
        bool $required = false,
               $default = null
    ): void {
    }

    public function test($value)
    {
        return $this->renderChildrenWithVariableOrReturnInput($value);
    }

    public static function testStatic($value, ?string $as, RenderingContextInterface $context, \Closure $closure)
    {
        return self::renderChildrenWithVariableOrReturnInputStatic($value, $as, $context, $closure);
    }

    public function buildRenderChildrenClosure(): \Closure
    {
        return function() {
            return '';
        };
    }
}
