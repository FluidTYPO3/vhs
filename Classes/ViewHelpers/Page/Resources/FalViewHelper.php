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
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
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
     * @return mixed
     * @throws Exception
     */
    public function render()
    {
        $record = $this->arguments['record'];
        $uid = $this->arguments['uid'];

        if (null === $record) {
            if (null === $uid) {
                $record = $this->getActiveRecord();
            } else {
                $record = $this->getRecord($uid);
            }
        }
        if (null === $record) {
            throw new Exception('No record was found. The "record" or "uid" argument must be specified.', 1475675124);
        }

        $record = $this->getSlideRecords($record['uid'], $this->arguments['limit'])[0];

        // attempt to load resources. If any Exceptions happen, transform them to
        // ViewHelperExceptions which render as an inline text error message.
        try {
            $resources = $this->getResources($record);
        } catch (\Exception $error) {
            // we are doing the pokemon-thing and catching the very top level
            // of Exception because the range of Exceptions that are possibly
            // thrown by the getResources() method in subclasses are not
            // extended from a shared base class like RuntimeException. Thus,
            // we are forced to "catch them all" - but we also output them.
            throw new Exception($error->getMessage(), $error->getCode());
        }
        return $this->renderChildrenWithVariableOrReturnInput($resources);
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
     * @param integer $pageUid
     * @param integer $limit
     * @return array
     */
    protected function getSlideRecordsFromPage($pageUid, $limit)
    {
        $pageRecord = $this->getRecord($pageUid);
        $resources = $this->getResources($pageRecord);
        return count($resources) > 0 ? [$pageRecord] : [];
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
