<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Resource\Record\FalViewHelper as ResourcesFalViewHelper;
use FluidTYPO3\Vhs\Traits\SlideViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Page FAL resource ViewHelper.
 */
class FalViewHelper extends ResourcesFalViewHelper
{

    use SlideViewHelperTrait;

    const DEFAULT_TABLE = 'pages';
    const DEFAULT_FIELD = 'media';

    /**
     * @var string
     */
    protected $table = self::DEFAULT_TABLE;

    /**
     * @var string
     */
    protected $field = self::DEFAULT_FIELD;

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->overrideArgument('table', 'string', 'The table to lookup records.', false, self::DEFAULT_TABLE);
        $this->overrideArgument(
            'field',
            'string',
            'The field of the table associated to resources.',
            false,
            self::DEFAULT_FIELD
        );
        $this->registerSlideArguments();
    }

    /**
     * @param integer $id
     * @return array
     */
    public function getRecord($id)
    {
        $record = parent::getRecord($id);
        if (!$this->isDefaultLanguage()) {
            if (TYPO3_MODE === 'FE') {
                $pageRepository = $GLOBALS['TSFE']->sys_page;
            } else {
                $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
                $pageRepository->init(false);
            }
            /** @var PageRepository $pageRepository */
            $localisation = $pageRepository->getPageOverlay($record, $this->getCurrentLanguageUid());
            if (is_array($localisation)) {
                $record = $localisation;
            }
        }
        return $record;
    }

    /**
     * @param array $record
     * @return array
     * @throws \Exception
     */
    public function getResources($record)
    {
        return $this->getSlideRecords($record['uid']);
    }

    /**
     * @param integer $pageUid
     * @param integer $limit
     * @return array
     */
    protected function getSlideRecordsFromPage($pageUid, $limit)
    {
        $pageRecord = $this->getRecord($pageUid);
        // NB: we call parent::getResources intentionally, as to not call the overridden
        // method on this class. Calling $this->getResources() would yield wrong result
        // for the purpose of this method.
        $resources = parent::getResources($pageRecord);
        if (null !== $limit && count($resources) > $limit) {
            $resources = array_slice($resources, 0, $limit);
        }
        return $resources;
    }

    /**
     * @return boolean
     */
    protected function isDefaultLanguage()
    {
        return $this->getCurrentLanguageUid() === 0;
    }

    /**
     * @return integer
     */
    protected function getCurrentLanguageUid()
    {
        return (integer) $GLOBALS['TSFE']->sys_language_uid;
    }

    /**
     * AbstractRecordResource usually uses the current cObj as reference,
     * but the page is needed here
     *
     * @return array
     */
    public function getActiveRecord()
    {
        return $GLOBALS['TSFE']->page;
    }
}
