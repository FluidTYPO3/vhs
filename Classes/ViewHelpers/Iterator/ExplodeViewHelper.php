<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\BasicViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Explode ViewHelper
 *
 * Explodes a string by $glue.
 */
class ExplodeViewHelper extends AbstractViewHelper
{

    use BasicViewHelperTrait;
    use TemplateVariableViewHelperTrait;

    /**
     * @var string
     */
    protected $method = 'explode';

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerAsArgument();
        $this->registerArgument('content', 'string', 'String to be exploded by glue');
        $this->registerArgument(
            'glue',
            'string',
            'String used as glue in the string to be exploded. Use glue value of "constant:NAMEOFCONSTANT" ' .
            '(fx "constant:LF" for linefeed as glue)',
            false,
            ','
        );
    }

    /**
     * Render method
     *
     * @return mixed
     */
    public function render()
    {
        $content = $this->getArgumentFromArgumentsOrTagContent('content');
        $glue = $this->resolveGlue();
        $output = call_user_func_array($this->method, [$glue, $content]);
        return $this->renderChildrenWithVariableOrReturnInput($output);
    }

    /**
     * Detects the proper glue string to use for implode/explode operation
     *
     * @return string
     */
    protected function resolveGlue()
    {
        $glue = $this->arguments['glue'];
        if (false !== strpos($glue, ':') && 1 < strlen($glue)) {
            // glue contains a special type identifier, resolve the actual glue
            list ($type, $value) = explode(':', $glue);
            switch ($type) {
                case 'constant':
                    $glue = constant($value);
                    break;
                default:
                    $glue = $value;
            }
        }
        return $glue;
    }
}
