<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Content\Resources;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\ArgumentOverride;

/**
 * Content FAL relations ViewHelper
 *
 * ### Render a single image in a content element
 *
 * We assume that the flux content element has an IRRE file field
 * `<flux:field.inline.fal name="settings.image">`.
 *
 * The file data can be loaded and displayed with:
 *
 * ```
 * {v:content.resources.fal(field: 'settings.image')
 *   -> v:iterator.first()
 *   -> v:variable.set(name: 'image')}
 * <f:if condition="{image}">
 *   <f:image src="{image.uid}"/>
 * </f:if>
 * ```
 *
 *
 * ### Image preview in backend
 *
 * To load image data for the "Preview" section in the backend's page view,
 * you have to pass the `record` attribute:
 *
 * ```
 * {v:content.resources.fal(field: 'settings.image', record: record)}
 * ```
 */
class FalViewHelper extends \FluidTYPO3\Vhs\ViewHelpers\Resource\Record\FalViewHelper
{
    use ArgumentOverride;

    const DEFAULT_TABLE = 'tt_content';
    const DEFAULT_FIELD = 'image';

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
