<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * ViewHelper to make a breadcrumb link set from a pageUid, automatic or manual.
 */
class BreadCrumbViewHelper extends AbstractMenuViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'pageUid',
            'integer',
            'Optional parent page UID to use as top level of menu. If left out will be detected from ' .
            'rootLine using $entryLevel.'
        );
        $this->registerArgument(
            'endLevel',
            'integer',
            'Optional deepest level of rendering. If left out all levels up to the current are rendered.'
        );
        $this->overrideArgument(
            'as',
            'string',
            'If used, stores the menu pages as an array in a variable named after this value and renders the tag ' .
            'content. If the tag content is empty automatic rendering is triggered.',
            false,
            'breadcrumb'
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        $pageUid = $this->arguments['pageUid'] > 0 ? $this->arguments['pageUid'] : $GLOBALS['TSFE']->id;
        $entryLevel = $this->arguments['entryLevel'];
        $endLevel = $this->arguments['endLevel'];
        $rawRootLineData = $this->pageService->getRootLine($pageUid);
        $rawRootLineData = array_reverse($rawRootLineData);
        $rawRootLineData = array_slice($rawRootLineData, $entryLevel, $endLevel);
        $rootLineData = [];
        $showHidden = (boolean) $this->arguments['showHiddenInMenu'];
        foreach ($rawRootLineData as $record) {
            $isHidden = (boolean) $record['nav_hide'];

            if (true === (boolean) $this->arguments['includeSpacers']) {
                $isAllowedDoktype = (int) $record['doktype'] <= PageRepository::DOKTYPE_SPACER;
            } else {
                $isAllowedDoktype = (int) $record['doktype'] < PageRepository::DOKTYPE_SPACER;
            }

            if ((true === $showHidden && true === $isHidden || false === $isHidden) && true === $isAllowedDoktype) {
                array_push($rootLineData, $record);
            }
        }
        $rootLine = $this->parseMenu($rootLineData);
        if (0 === count($rootLine)) {
            return null;
        }
        $this->backupVariables();
        $this->templateVariableContainer->add($this->arguments['as'], $rootLine);
        $output = $this->renderContent($rootLine);
        $this->templateVariableContainer->remove($this->arguments['as']);
        $this->restoreVariables();

        return $output;
    }
}
