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
use TYPO3\CMS\Extbase\Object\ObjectManager;

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
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerSlideArguments();
    }

    /**
     * Register the "limit", "slide", "slideCollect" and "slideCollectReverse"
     * arguments which are consumed by getSlideRecords.
     * Should be used inside registerArguments().
     *
     * @return void
     * @api
     */
    protected function registerSlideArguments()
    {
        $this->registerArgument('limit', 'integer', 'Optional limit to the total number of records to render');
        $this->registerArgument(
            'slide',
            'integer',
            'Enables Record Sliding - amount of levels which shall get walked up the rootline, including the ' .
            'current page. For infinite sliding (till the rootpage) set to -1. Only the first PID which has at ' .
            'minimum one record is used',
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
            'by setting this flag))',
            false,
            false
        );
    }

    /**
     * @return PageService
     */
    protected function getPageService()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        return $objectManager->get(PageService::class);
    }

    /**
     * Get a number of records from a page for sliding
     *
     * @param integer $pageUid PID to get the records from
     * @param integer $limit number of records to get at maximum
     */
    abstract protected function getSlideRecordsFromPage($pageUid, $limit);

    /**
     * Get records, optionally sliding up the page rootline
     *
     * @param integer $pageUid
     * @param integer $limit
     * @return array
     * @api
     */
    protected function getSlideRecords($pageUid, $limit = null)
    {
        if (null === $limit && false === empty($this->arguments['limit'])) {
            $limit = (integer) $this->arguments['limit'];
        }

        $slide = (integer) $this->arguments['slide'];
        $slideCollectReverse = (boolean) $this->arguments['slideCollectReverse'];
        $slideCollect = (integer) $this->arguments['slideCollect'];

        if ($slideCollect && !$slide) {
            $slide = $slideCollect;
        }

        // find out which storage page UIDs to read from, respecting slide depth
        $storagePageUids = [];
        if (0 === $slide) {
            $storagePageUids[] = $pageUid;
        } else {
            $reverse = false;
            if (true === $slideCollectReverse && 0 !== $slideCollect) {
                $reverse = true;
            }
            $rootLine = $this->getPageService()->getRootLine($pageUid, null);
            if (-1 !== $slide) {
                $rootLine = array_slice($rootLine, 0, $slide);
            }
            if ($reverse) {
                $rootLine = array_reverse($rootLine);
            }
            foreach ($rootLine as $page) {
                $storagePageUids[] = (integer) $page['uid'];
            }
        }
        // select records, respecting slide and slideCollect.
        $records = [];
        do {
            $storagePageUid = array_shift($storagePageUids);
            $limitRemaining = null;
            if (null !== $limit) {
                $limitRemaining = $limit - count($records);
                if (0 >= $limitRemaining) {
                    break;
                }
            }
            $recordsFromPageUid = $this->getSlideRecordsFromPage($storagePageUid, $limitRemaining);
            if (0 < count($recordsFromPageUid)) {
                $records = array_merge($records, $recordsFromPageUid);
                if (0 === $slideCollect) {
                    // stop collecting because argument said so and we've gotten at least one record now.
                    break;
                }
            }
        } while (false === empty($storagePageUids));

        return $records;
    }
}
