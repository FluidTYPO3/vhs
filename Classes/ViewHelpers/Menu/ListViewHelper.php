<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Menu;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * ### Page: List Menu ViewHelper
 *
 * ViewHelper for rendering TYPO3 list menus in Fluid
 *
 * Supports both automatic, tag-based rendering (which
 * defaults to `ul > li` with options to set both the
 * parent and child tag names. When using manual rendering
 * a range of support CSS classes are available along
 * with each page record.
 */
class ListViewHelper extends AbstractMenuViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'pages',
            'mixed',
            'Page UIDs to include in the menu. Can be CSV, array or an object implementing Traversable.',
            true
        );
    }

    /**
     * @return null|string
     */
    public function render()
    {
        $pages = $this->processPagesArgument();
        if (0 === count($pages)) {
            return null;
        }
        $showAccessProtected = (boolean) $this->arguments['showAccessProtected'];
        $menuData = [];
        foreach ($pages as $pageUid) {
            $row = $this->pageService->getPage($pageUid, $showAccessProtected);
            if (!empty($row)) {
                $menuData[] = $row;
            }
        }
        $menu = $this->parseMenu($menuData);
        $this->backupVariables();
        $variableProvider = $this->renderingContext->getVariableProvider();
        /** @var string $as */
        $as = $this->arguments['as'];
        $variableProvider->add($as, $menu);
        $output = $this->renderContent($menu);
        $variableProvider->remove($as);
        $this->restoreVariables();

        return $output;
    }
}
