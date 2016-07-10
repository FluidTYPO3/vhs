<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\DefaultRenderMethodViewHelperTrait;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to access data of the current page record.
 */
class InfoViewHelper extends AbstractViewHelper
{

    use DefaultRenderMethodViewHelperTrait;
    use TemplateVariableViewHelperTrait;

    /**
     * @var PageService
     */
    protected static $pageService;

    public function initializeArguments()
    {
        $this->registerAsArgument();
        $this->registerArgument(
            'pageUid',
            'integer',
            'If specified, this UID will be used to fetch page data instead of using the current page.',
            false,
            0
        );
        $this->registerArgument(
            'field',
            'string',
            'If specified, only this field will be returned/assigned instead of the complete page record.'
        );
    }

    /**
     * @return PageService
     */
    protected static function getPageService()
    {
        if (!static::$pageService) {
            static::$pageService = GeneralUtility::makeInstance(ObjectManager::class)->get(PageService::class);
        }
        return static::$pageService;
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
        $pageUid = (integer) $arguments['pageUid'];
        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        $page = static::getPageService()->getPage($pageUid);
        $field = $arguments['field'];
        $content = null;
        if (true === empty($field)) {
            $content = $page;
        } elseif (true === isset($page[$field])) {
            $content = $page[$field];
        }

        return static::renderChildrenWithVariableOrReturnInputStatic(
            $content,
            $arguments['as'],
            $renderingContext,
            $renderChildrenClosure
        );
    }
}
