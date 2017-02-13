<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use FluidTYPO3\Vhs\Utility\ErrorUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to access data of the current page record.
 */
class InfoViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;
    use TemplateVariableViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
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
        if (!isset($GLOBALS['TSFE']) || !$GLOBALS['TSFE']->sys_page instanceof PageRepository) {
            ErrorUtility::throwViewHelperException(
                sprintf('ViewHelper %s does not work in backend context without a simulated frontend.', static::class),
                1489931508
            );
        }
        $pageUid = (integer) $arguments['pageUid'];
        if (0 === $pageUid) {
            $pageUid = $GLOBALS['TSFE']->id;
        }
        $page = $GLOBALS['TSFE']->sys_page->getPage_noCheck($pageUid);
        $field = $arguments['field'];
        $content = null;
        if (true === empty($field)) {
            $content = $page;
        } elseif (true === is_array($page) && true === isset($page[$field])) {
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
