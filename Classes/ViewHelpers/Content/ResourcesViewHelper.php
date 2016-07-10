<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\ViewHelpers\Resource\RecordViewHelper;

/**
 * Resources ViewHelper
 *
 * Loads FAL records associated with records of arbitrary types.
 */
class ResourcesViewHelper extends RecordViewHelper
{

    const DEFAULT_TABLE = 'tt_content';
    const DEFAULT_FIELD = 'image';

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
    }
}
