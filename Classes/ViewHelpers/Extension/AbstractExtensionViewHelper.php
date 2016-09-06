<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Extension;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Base class: Extension ViewHelpers
 */
abstract class AbstractExtensionViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('extensionName', 'string', 'Name, in UpperCamelCase, of the extension to be checked');
    }

    /**
     * @return string
     */
    protected function getExtensionKey()
    {
        $extensionName = $this->getExtensionName();
        return GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
    }

    /**
     * @throws \RuntimeException
     * @return mixed
     */
    protected function getExtensionName()
    {
        if (isset($this->arguments['extensionName']) && !empty($this->arguments['extensionName'])) {
            return $this->arguments['extensionName'];
        }
        $request = $this->controllerContext->getRequest();
        $extensionName = $request->getControllerExtensionName();
        if (empty($extensionName)) {
            throw new \RuntimeException(
                'Unable to read extension name from ControllerContext and value not manually specified',
                1364167519
            );
        }
        return $extensionName;
    }
}
