<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\View\UncacheTemplateView;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Uncaches partials. Use like ``f:render``.
 * The partial will then be rendered each time.
 * Please be aware that this will impact render time.
 * Arguments must be serializable and will be cached.
 */
class UncacheViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('partial', 'string', 'Reference to a partial.', true);
        $this->registerArgument('section', 'string', 'Name of section inside the partial to render.', false, null);
        $this->registerArgument('arguments', 'array', 'Arguments to pass to the partial.', false, null);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $templateVariableContainer = $renderingContext->getTemplateVariableContainer();
        $partialArguments = $arguments['arguments'];
        if (false === is_array($partialArguments)) {
            $partialArguments = [];
        }
        if (false === isset($partialArguments['settings']) && true === $templateVariableContainer->exists('settings')) {
            $partialArguments['settings'] = $templateVariableContainer->get('settings');
        }

        $substKey = 'INT_SCRIPT.' . $GLOBALS['TSFE']->uniqueHash();
        $content = '<!--' . $substKey . '-->';

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var UncacheTemplateView $templateView */
        $templateView = $objectManager->get(UncacheTemplateView::class);

        $GLOBALS['TSFE']->config['INTincScript'][$substKey] = [
            'type' => 'POSTUSERFUNC',
            'cObj' => serialize($templateView),
            'postUserFunc' => 'render',
            'conf' => [
                'partial' => $arguments['partial'],
                'section' => $arguments['section'],
                'arguments' => $partialArguments,
                'controllerContext' => $renderingContext->getControllerContext()
            ],
            'content' => $content
        ];

        return $content;
    }
}
