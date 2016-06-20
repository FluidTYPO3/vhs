<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content\Random;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Content\AbstractContentViewHelper;

/**
 * ViewHelper for rendering a random content element in Fluid page templates.
 */
class RenderViewHelper extends AbstractContentViewHelper
{

    /**
     * Initialize ViewHelper arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->overrideArgument('limit', 'integer', 'Optional limit number of content elements to render', false, 1);
    }

    /**
     * Render method
     *
     * @return mixed
     */
    public function render()
    {
        if ('BE' === TYPO3_MODE) {
            return '';
        }
        // Remove limit for getContentRecords()
        $limit = $this->arguments['limit'];
        $this->arguments['limit'] = null;
        // Just using getContentRecords with a limit of 1 would not support
        // using slideCollect as collecting would stop as soon as one record
        // was found. As a potential optimization, $render could be overrided
        // so all the content records that end up unused do not get rendered.
        $contentRecords = $this->getContentRecords();
        if (false === empty($contentRecords)) {
            shuffle($contentRecords);
            $contentRecords = array_slice($contentRecords, 0, $limit);
            if (true === (boolean) $this->arguments['render']) {
                $contentRecords = implode(LF, $contentRecords);
            }
        }
        return $contentRecords;
    }
}
