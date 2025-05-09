<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArgumentOverride;
use FluidTYPO3\Vhs\ViewHelpers\Resource\RecordViewHelper;

/**
 * Page FAL resources ViewHelper.
 */
class ResourcesViewHelper extends RecordViewHelper
{
    use ArgumentOverride;

    const DEFAULT_TABLE = 'pages';
    const DEFAULT_FIELD = 'media';

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
    }
}
