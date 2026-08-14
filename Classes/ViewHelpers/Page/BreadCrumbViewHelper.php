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
use FluidTYPO3\Vhs\Utility\RequestResolver;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use Psr\Http\Message\ServerRequestInterface;

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

    public function render(): string
    {
        $pageUid = (int) (
            $this->arguments['pageUid'] > 0
            ? $this->arguments['pageUid']
            : RequestResolver::getPageUid()
        );
        /** @var int $entryLevel */
        $entryLevel = $this->arguments['entryLevel'];
        /** @var int|null $endLevel */
        $endLevel = $this->arguments['endLevel'];
        $rawRootLineData = $this->pageService->getRootLine($pageUid);
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
        $this->renderingContext->getVariableProvider()->add($as, $rootLine);
        $output = $this->renderContent($rootLine);
        $this->renderingContext->getVariableProvider()->remove($as);
        $this->restoreVariables();

        return $output;
    }

    private function getRequest(): ServerRequestInterface|null
    {
        if ($this->renderingContext->hasAttribute(ServerRequestInterface::class)) {
            return $this->renderingContext->getAttribute(ServerRequestInterface::class);
        }
        return null;
    }
}
