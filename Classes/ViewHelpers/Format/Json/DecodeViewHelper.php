<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Format\Json;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Converts the JSON encoded argument into a PHP variable.
 */
class DecodeViewHelper extends AbstractViewHelper
{

    /**
     * @param string $json
     * @throws Exception
     * @return mixed
     */
    public function render($json = null)
    {
        if (null === $json) {
            $json = $this->renderChildren();
            if (true === empty($json)) {
                return null;
            }
        }

        $value = json_decode($json, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception('The provided argument is invalid JSON.', 1358440054);
        }

        return $value;
    }
}
