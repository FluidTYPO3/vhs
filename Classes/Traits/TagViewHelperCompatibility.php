<?php
namespace FluidTYPO3\Vhs\Traits;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\VersionNumberUtility;

trait TagViewHelperCompatibility
{
    /**
     * Register a new tag attribute. Tag attributes are all arguments which will be directly appended to a tag if you
     * call $this->initializeTag()
     *
     * @param string $name Name of tag attribute
     * @param string $type Type of the tag attribute
     * @param string $description Description of tag attribute
     * @param bool $required set to true if tag attribute is required. Defaults to false.
     * @param mixed $defaultValue Optional, default value of attribute if one applies
     * @return void
     * @api
     */
    protected function registerTagAttribute($name, $type, $description, $required = false, $defaultValue = null)
    {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '13.4', '>=')) {
            $this->registerArgument($name, $type, $description, $required, $defaultValue);
            return;
        }
        parent::registerTagAttribute($name, $type, $description, $required, $defaultValue);
    }

    /**
     * Registers all standard HTML universal attributes.
     * Should be used inside registerArguments();
     *
     * @return void
     * @api
     */
    protected function registerUniversalTagAttributes()
    {
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '13.4', '>=')) {
            return;
        }
        $this->registerTagAttribute('class', 'string', 'CSS class(es) for this element');
        $this->registerTagAttribute(
            'dir',
            'string',
            'Text direction for this HTML element. Allowed strings: "ltr" (left to right), "rtl" (right to left)'
        );
        $this->registerTagAttribute('id', 'string', 'Unique (in this file) identifier for this HTML element.');
        $this->registerTagAttribute(
            'lang',
            'string',
            'Language for this element. Use short names specified in RFC 1766'
        );
        $this->registerTagAttribute('style', 'string', 'Individual CSS styles for this element');
        $this->registerTagAttribute('title', 'string', 'Tooltip text of element');
        $this->registerTagAttribute('accesskey', 'string', 'Keyboard shortcut to access this element');
        $this->registerTagAttribute('tabindex', 'integer', 'Specifies the tab order of this element');
        $this->registerTagAttribute('onclick', 'string', 'JavaScript evaluated for the onclick event');
    }
}
