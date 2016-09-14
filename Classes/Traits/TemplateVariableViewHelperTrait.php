<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class TemplateVariableViewHelperTrait
 *
 * Trait implementable by ViewHelpers which operate with
 * template variables in one way or another. Contains
 * the following main responsibilities:
 *
 * - A generic "as" argument solution
 * - A method to render child content with automatically
 *   backed up variables specified in an array.
 */
trait TemplateVariableViewHelperTrait
{

    /**
     * Default initialisation of arguments - will be used
     * if the implementing ViewHelper does not itself define
     * this method. The default behavior is to only register
     * the "as" argument.
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerAsArgument();
    }

    /**
     * Registers the "as" argument for use with the
     * implementing ViewHelper.
     *
     * @return void
     */
    protected function registerAsArgument()
    {
        $this->registerArgument(
            'as',
            'string',
            'Template variable name to assign; if not specified the ViewHelper returns the variable instead.'
        );
    }

    /**
     * @param string $variable
     *
     * @return mixed
     */
    protected function renderChildrenWithVariableOrReturnInput($variable = null)
    {
        $as = $this->arguments['as'];
        if (true === empty($as)) {
            return $variable;
        } else {
            $variables = [$as => $variable];
            $content = $this->renderChildrenWithVariables($variables);
        }
        return $content;
    }

    /**
     * @param mixed $variable
     * @param string $as
     * @param \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @param \Closure $renderChildrenClosure
     * @return mixed
     */
    protected static function renderChildrenWithVariableOrReturnInputStatic(
        $variable,
        $as,
        RenderingContextInterface $renderingContext,
        \Closure $renderChildrenClosure
    ) {
        if (true === empty($as)) {
            return $variable;
        } else {
            $variables = [$as => $variable];
            $content = static::renderChildrenWithVariablesStatic(
                $variables,
                $renderingContext->getTemplateVariableContainer(),
                $renderChildrenClosure
            );
        }
        return $content;
    }

    /**
     * Renders tag content of ViewHelper and inserts variables
     * in $variables into $variableContainer while keeping backups
     * of each existing variable, restoring it after rendering.
     * Returns the output of the renderChildren() method on $viewHelper.
     *
     * @param array $variables
     * @return mixed
     */
    protected function renderChildrenWithVariables(array $variables)
    {
        return static::renderChildrenWithVariablesStatic(
            $variables,
            $this->templateVariableContainer,
            $this->buildRenderChildrenClosure()
        );
    }

    /**
     * Renders tag content of ViewHelper and inserts variables
     * in $variables into $variableContainer while keeping backups
     * of each existing variable, restoring it after rendering.
     * Returns the output of the renderChildren() method on $viewHelper.
     *
     * @param array $variables
     * @param \TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer $templateVariableContainer
     * @param \Closure $renderChildrenClosure
     * @return mixed
     */
    protected static function renderChildrenWithVariablesStatic(
        array $variables,
        $templateVariableContainer,
        $renderChildrenClosure
    ) {
        $backups = static::backupVariables($variables, $templateVariableContainer);
        $content = $renderChildrenClosure();
        static::restoreVariables($variables, $backups, $templateVariableContainer);
        return $content;
    }

    /**
     * @param array $variables
     * @param \TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer $templateVariableContainer
     * @return array
     */
    private static function backupVariables(array $variables, $templateVariableContainer)
    {
        $backups = [];
        foreach ($variables as $variableName => $variableValue) {
            if (true === $templateVariableContainer->exists($variableName)) {
                $backups[$variableName] = $templateVariableContainer->get($variableName);
                $templateVariableContainer->remove($variableName);
            }
            $templateVariableContainer->add($variableName, $variableValue);
        }
        return $backups;
    }

    /**
     * @param array $variables
     * @param array $backups
     * @param \TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer $templateVariableContainer
     * @return void
     */
    private static function restoreVariables(array $variables, array $backups, $templateVariableContainer)
    {
        foreach ($variables as $variableName => $variableValue) {
            $templateVariableContainer->remove($variableName);
            if (true === isset($backups[$variableName])) {
                $templateVariableContainer->add($variableName, $variableValue);
            }
        }
    }
}
