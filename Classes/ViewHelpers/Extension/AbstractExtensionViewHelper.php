<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Extension;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Base class: Extension ViewHelpers
 */
abstract class AbstractExtensionViewHelper extends AbstractViewHelper
{
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('extensionName', 'string', 'Name, in UpperCamelCase, of the extension to be checked');
    }

    /**
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    protected static function getExtensionKey(array $arguments, RenderingContextInterface $renderingContext)
    {
        $extensionName = static::getExtensionName($arguments, $renderingContext);
        return GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
    }

    /**
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @throws \RuntimeException
     * @return mixed
     */
    protected static function getExtensionName(array $arguments, RenderingContextInterface $renderingContext)
    {
        if (isset($arguments['extensionName']) && !empty($arguments['extensionName'])) {
            return $arguments['extensionName'];
        }
        $request = $renderingContext->getControllerContext()->getRequest();
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
