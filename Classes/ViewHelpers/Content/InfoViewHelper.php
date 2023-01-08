<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Doctrine\DBAL\Result;
use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to access data of the current content element record.
 */
class InfoViewHelper extends AbstractViewHelper
{
    use TemplateVariableViewHelperTrait;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerAsArgument();
        $this->registerArgument(
            'contentUid',
            'integer',
            'If specified, this UID will be used to fetch content element data instead of using the current ' .
            'content element.',
            false,
            0
        );
        $this->registerArgument(
            'field',
            'string',
            'If specified, only this field will be returned/assigned instead of the complete content element record.'
        );
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function render()
    {
        $contentUid = (integer) $this->arguments['contentUid'];
        $record = false;

        if (0 === $contentUid) {
            /** @var ContentObjectRenderer $cObj */
            $cObj = $this->configurationManager->getContentObject();
            $record = $cObj->data;
        }

        $field = $this->arguments['field'];
        $selectFields = $field;

        if ($record === false && 0 !== $contentUid) {
            if (!isset($GLOBALS['TCA']['tt_content']['columns'][$field])) {
                $selectFields = '*';
            }

            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
            $queryBuilder->createNamedParameter($contentUid, \PDO::PARAM_INT, ':uid');

            /** @var Result $result */
            $result = $queryBuilder
                ->select($selectFields)
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->eq('uid', ':uid')
                )
                ->execute();
            /** @var array|null $record */
            $record = $result->fetchAssociative();

            // Add the page overlay
            if (class_exists(LanguageAspect::class)) {
                /** @var Context $context */
                $context = GeneralUtility::makeInstance(Context::class);
                /** @var LanguageAspect $languageAspect */
                $languageAspect = $context->getAspect('language');
                $languageUid = $languageAspect->getId();
            } else {
                $languageUid = $GLOBALS['TSFE']->sys_language_uid;
            }

            if (0 !== $languageUid && $GLOBALS['TSFE']->sys_language_contentOL) {
                $record = $GLOBALS['TSFE']->sys_page->getRecordOverlay(
                    'tt_content',
                    $record,
                    $GLOBALS['TSFE']->sys_language_content,
                    $GLOBALS['TSFE']->sys_language_contentOL
                );
            }
        }

        if (false === $record) {
            throw new \Exception(
                sprintf('Either record with uid %d or field %s do not exist.', $contentUid, $selectFields),
                1358679983
            );
        }

        // Check if single field or whole record should be returned
        $content = null;
        if (null === $field) {
            $content = $record;
        } elseif (true === isset($record[$field])) {
            $content = $record[$field];
        }

        return $this->renderChildrenWithVariableOrReturnInput($content);
    }
}
