<?php

namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use FluidTYPO3\Vhs\Traits\ArrayConsumingViewHelperTrait;

class DummyArrayConsumingViewHelper
{
    use ArrayConsumingViewHelperTrait;

    public array $arguments = [];

    /**
     * @var mixed
     */
    public $value = null;

    public function execute(): array
    {
        return $this->getArgumentFromArgumentsOrTagContentAndConvertToArray('value');
    }

    public function merge(array $a, array $b): array
    {
        return $this->mergeArrays($a, $b);
    }

    public function buildRenderChildrenClosure(): \Closure
    {
        $value = $this->value;
        return function() use ($value) {
            return $value;
        };
    }
}
