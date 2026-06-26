<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Service\PageService;
use FluidTYPO3\Vhs\Traits\ArgumentOverride;
use FluidTYPO3\Vhs\Traits\SlideViewHelperTrait;
use FluidTYPO3\Vhs\Utility\RequestResolver;
use FluidTYPO3\Vhs\ViewHelpers\Resource\Record\FalViewHelper as ResourcesFalViewHelper;
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
    use ArgumentOverride;

    public const string DEFAULT_TABLE = 'pages';
    public const string DEFAULT_FIELD = 'media';

    protected string $table = self::DEFAULT_TABLE;
    protected string $field = self::DEFAULT_FIELD;

    public function initializeArguments(): void
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

    public function getRecord(int $id): ?array
    {
        $record = parent::getRecord($id);
        if (!$this->isDefaultLanguage() && $record !== null) {
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

    public function getResources(array $record): array
    {
        return $this->getSlideRecords($record['uid']);
    }

    protected function getSlideRecordsFromPage(int $pageUid, ?int $limit): array
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

    protected function isDefaultLanguage(): bool
    {
        return $this->getCurrentLanguageUid() === 0;
    }

    protected function getCurrentLanguageUid(): int
    {
        return RequestResolver::getLanguage()->getLanguageId();
    }
}
