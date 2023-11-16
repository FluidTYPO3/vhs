<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SlideViewHelperTrait
 *
 * Trait implemented by ViewHelpers that wants some kind of
 * records to be optionally looked up and/or collected from
 * the current page and pages up the rootline. ViewHelpers must
 * implement the getSlideRecordsFromPage method which looks up
 * resources for a single page.
 *
 * Has the following main responsibilities:
 * - register arguments common for sliding
 * - method to get records with sliding according
 *   to the ViewHelper arguments
 */
trait SlideViewHelperTrait
{
    /**
     * Default initialisation of arguments - will be used
     * if the implementing ViewHelper does not itself define
     * this method.
     */
    public function initializeArguments(): void
    {
        $this->registerSlideArguments();
    }

    /**
     * Register the "limit", "slide", "slideCollect" and "slideCollectReverse"
     * arguments which are consumed by getSlideRecords.
     * Should be used inside registerArguments().
     */
    protected function registerSlideArguments(): void
    {
        $this->registerArgument('limit', 'integer', 'Optional limit to the total number of records to render');
        $this->registerArgument(
            'slide',
            'integer',
            'Enables Record Sliding - amount of levels which shall get walked up the rootline, including the ' .
            'current page. For infinite sliding (till the rootpage) set to -1. Only the first PID which has at ' .
            'minimum one record is used.',
            false,
            0
        );
        $this->registerArgument(
            'slideCollect',
            'integer',
            'If TRUE, content is collected up the root line. If FALSE, only the first PID which has content is ' .
            'used. If greater than zero, this value overrides $slide.',
            false,
            0
        );
        $this->registerArgument(
            'slideCollectReverse',
            'boolean',
            'Normally when collecting records the elements from the actual page get shown on the top and those ' .
            'from the parent pages below those. You can invert this behaviour (actual page elements at bottom) ' .
            'by setting this flag.',
            false,
            false
        );
    }

    abstract protected function getSlideRecordsFromPage(int $pageUid, ?int $limit): array;

    protected function getSlideRecords(int $pageUid, ?int $limit = null): array
    {
        /** @var int|null $limit */
        $limit = $limit ?? $this->arguments['limit'];
        /** @var int $slide */
        $slide = $this->arguments['slide'];
        $slideCollectReverse = (boolean) $this->arguments['slideCollectReverse'];
        /** @var int $slideCollect */
        $slideCollect = $this->arguments['slideCollect'];

        if ($slideCollect && !$slide) {
            $slide = $slideCollect;
        }

        // find out which storage page UIDs to read from, respecting slide depth

        if (0 === $slide) {
            return $this->getSlideRecordsFromPage($pageUid, $limit);
        }

        $reverse = $slideCollectReverse && 0 !== $slideCollect;
        $rootLine = $this->getPageService()->getRootLine($pageUid);
        if (-1 !== $slide) {
            $rootLine = array_slice($rootLine, 0, $slide);
        }
        if ($reverse) {
            $rootLine = array_reverse($rootLine);
        }

        $storagePageUids = [];
        foreach ($rootLine as $page) {
            $storagePageUids[] = (integer) $page['uid'];
        }

        // select records, respecting slide and slideCollect.
        $records = [];
        $limitRemaining = $limit;
        while (!empty($storagePageUids) && ($limitRemaining > 0 || !$limit)) {
            $storagePageUid = (integer) array_shift($storagePageUids);
            $recordsFromPageUid = $this->getSlideRecordsFromPage($storagePageUid, $limitRemaining);
            $numberOfReturnedRecords = count($recordsFromPageUid);
            if ($numberOfReturnedRecords > $limitRemaining && $limitRemaining !== null) {
                $recordsFromPageUid = array_slice($recordsFromPageUid, 0, $limitRemaining);
            }
            if ($limitRemaining !== null) {
                $limitRemaining -= count($recordsFromPageUid);
            }
            $records = array_merge($records, $recordsFromPageUid);
            if (count($records) > 0 && 0 === $slideCollect) {
                // stop collecting because argument said so and we've gotten at least one record now.
                break;
            }
        }

        return $records;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getPageService(): PageService
    {
        /** @var PageService $pageService */
        $pageService = GeneralUtility::makeInstance(PageService::class);
        return $pageService;
    }
}
