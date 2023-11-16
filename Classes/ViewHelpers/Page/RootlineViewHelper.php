<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to get the rootline of a page.
 */
class RootlineViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;
    use TemplateVariableViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerAsArgument();
        $this->registerArgument('pageUid', 'integer', 'Optional page uid to use.');
    }

    /**
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var int $pageUid */
        $pageUid = $arguments['pageUid'];
        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        /** @var string $as */
        $as = $arguments['as'];
        return static::renderChildrenWithVariableOrReturnInputStatic(
            static::getPageService()->getRootLine($pageUid),
            $as,
            $renderingContext,
            $renderChildrenClosure
        );
    }

    protected static function getPageService(): PageService
    {
        /** @var PageService $pageService */
        $pageService = GeneralUtility::makeInstance(PageService::class);
        return $pageService;
    }
}
