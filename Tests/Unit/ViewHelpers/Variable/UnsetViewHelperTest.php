<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class UnsetViewHelperTest
 */
class UnsetViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canUnsetVariable()
    {
        $variables = new \ArrayObject(['test' => 'test']);
        $instance = $this->buildViewHelperInstance(['name' => 'test']);
        $context = ObjectAccess::getProperty($instance, 'renderingContext', true);
        if (\method_exists($context, 'getVariableProvider')) {
            $provider = $context->getVariableProvider();
        } else {
            $provider = $context->getTemplateVariableContainer();
        }
        $provider['test'] = 'test';
        $instance->initializeArgumentsAndRender();
        $this->assertNotContains('test', $provider->getAll());
    }
}
