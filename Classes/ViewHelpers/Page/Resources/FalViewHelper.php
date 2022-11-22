<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\SlideViewHelperTrait;
use FluidTYPO3\Vhs\ViewHelpers\Resource\Record\FalViewHelper as ResourcesFalViewHelper;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Page FAL resource ViewHelper.
 *
 * Do not use the "uid" argument in the "Preview" section.
 * Instead, use the "record" argument and pass the entire record.
 * This bypasses visibility restrictions that normally apply when you attempt
 * to load a record by UID through TYPO3's PageRepository, which is what the
 * resource ViewHelpers do if you only pass uid.
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

        $this->overrideArgument('table', 'string', 'The table to lookup records.', false, static::DEFAULT_TABLE);
        $this->overrideArgument(
            'field',
            'string',
            'The field of the table associated to resources.',
            false,
            static::DEFAULT_FIELD
        );
        $this->registerSlideArguments();
    }

    /**
     * @param integer $id
     * @return array|null
     */
    public function getRecord($id)
    {
        $record = parent::getRecord($id);
        if (!$this->isDefaultLanguage()) {
            /** @var PageService $pageService */
            $pageService = GeneralUtility::makeInstance(PageService::class);
            $pageRepository = $pageService->getPageRepository();
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
        if ($pageRecord === null) {
            return [];
        }
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
        if (class_exists(LanguageAspect::class)) {
            /** @var Context $context */
            $context = GeneralUtility::makeInstance(Context::class);
            /** @var LanguageAspect $languageAspect */
            $languageAspect = $context->getAspect('language');
            $languageUid = $languageAspect->getId();
        } else {
            $languageUid = $GLOBALS['TSFE']->sys_language_uid;
        }

        return (integer) $languageUid;
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
