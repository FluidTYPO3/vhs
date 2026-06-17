<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArgumentOverride;
use FluidTYPO3\Vhs\ViewHelpers\Menu\AbstractMenuViewHelper;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;

/**
 * ViewHelper to make a breadcrumb link set from a pageUid, automatic or manual.
 */
class BreadCrumbViewHelper extends AbstractMenuViewHelper
{
    use ArgumentOverride;

    public function initializeArguments(): void
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
    public function render(): string
    {
        $pageUid = $this->arguments['pageUid'] ?? 0;
        if (!is_int($pageUid)) {
            $pageUid = is_numeric($pageUid) ? (int) (string) $pageUid : 0;
        }
        /** @var int $entryLevel */
        $entryLevel = $this->arguments['entryLevel'];
        /** @var int|null $endLevel */
        $endLevel = $this->arguments['endLevel'];
        $resolvedPageUid = $pageUid > 0 ? (int) $pageUid : null;
        $rawRootLineData = $this->pageService->getRootLine($resolvedPageUid);
        $rawRootLineData = array_reverse($rawRootLineData);
        $rawRootLineData = array_slice($rawRootLineData, $entryLevel, $endLevel);
        $rootLineData = [];
        $showHidden = (bool) $this->arguments['showHiddenInMenu'];
        $spacerDoktype = PageRepository::DOKTYPE_SPACER;
        foreach ($rawRootLineData as $record) {
            $isHidden = (bool) $record['nav_hide'];

            if ($this->arguments['includeSpacers']) {
                $isAllowedDoktype = (int) $record['doktype'] <= $spacerDoktype;
            } else {
                $isAllowedDoktype = (int) $record['doktype'] < $spacerDoktype;
            }

            if (($showHidden && $isHidden || !$isHidden) && $isAllowedDoktype) {
                $rootLineData[] = $record;
            }
        }
        $rootLine = $this->parseMenu($rootLineData);
        if (0 === count($rootLine)) {
            return '';
        }
        $this->backupVariables();
        /** @var string $as */
        $as = $this->arguments['as'];
        $variableProvider = $this->getRenderingContextOrFail()->getVariableProvider();
        $variableProvider->add($as, $rootLine);
        $output = $this->renderContent($rootLine);
        $variableProvider->remove($as);
        $this->restoreVariables();

        return $output;
    }
}
