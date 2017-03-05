<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TemplateVariableViewHelperTrait;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface An instance of the Configuration Manager
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
        if (0 === $contentUid) {
            $cObj = $this->configurationManager->getContentObject();
            $record = $cObj->data;
        }

        $field = $this->arguments['field'];

        if (false === isset($record) && 0 !== $contentUid) {
            if (null !== $field && true === isset($GLOBALS['TCA']['tt_content']['columns'][$field])) {
                $selectFields = $field;
            } else {
                $selectFields = '*';
            }
            $record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
                $selectFields,
                'tt_content',
                sprintf('uid=%d', $contentUid)
            );

            // Add the page overlay
            $languageUid = (integer) $GLOBALS['TSFE']->sys_language_uid;
            if (0 !== $languageUid && $GLOBALS['TSFE']->sys_language_contentOL) {
                $record = $GLOBALS['TSFE']->sys_page->getRecordOverlay(
                    'tt_content',
                    $record,
                    $GLOBALS['TSFE']->sys_language_content,
                    $GLOBALS['TSFE']->sys_language_contentOL
                );
            }
        }

        if (false === $record && false === isset($record)) {
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
