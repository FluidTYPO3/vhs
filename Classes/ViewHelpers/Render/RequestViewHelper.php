<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Render;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Dispatcher;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * ### Render: Request
 *
 * Renders a sub-request to the desired Extension, Plugin,
 * Controller and action with the desired arguments.
 *
 * Note: arguments must not be wrapped with the prefix used
 * in GET/POST parameters but must be provided as if the
 * arguments were sent directly to the Controller action.
 *
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 * @subpackage ViewHelpers\Render
 */
class RequestViewHelper extends AbstractRenderViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Mvc\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @var string
	 */
	protected $requestType = 'TYPO3\CMS\Extbase\Mvc\Web\Request';

	/**
	 * @var string
	 */
	protected $responseType = 'TYPO3\CMS\Extbase\Mvc\Web\Response';

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\Dispatcher $dispatcher
	 * @return void
	 */
	public function injectDispatcher(Dispatcher $dispatcher) {
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Dispatch Request
	 *
	 * Dispatches (as a completely new Request) a Request that will
	 * execute a configured Plugin->Controller->action() which means
	 * that the Plugin, Controller and Action you use must be allowed
	 * by the plugin configuration of the target controller.
	 *
	 * @param string|NULL $action
	 * @param string|NULL $controller
	 * @param string|NULL $extensionName
	 * @param string|NULL $pluginName
	 * @param string|NULL $vendorName
	 * @param array $arguments
	 * @return \TYPO3\CMS\Extbase\Mvc\ResponseInterface
	 * @throws \Exception
	 * @api
	 */
	public function render(
			$action = NULL,
			$controller = NULL,
			$extensionName = NULL,
			$pluginName = NULL,
			$vendorName = NULL,
			array $arguments = array()
	) {
		$contentObjectBackup = $this->configurationManager->getContentObject();
		if (TRUE === isset($this->request)) {
			$configurationBackup = $this->configurationManager->getConfiguration(
				ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
				$this->request->getControllerExtensionName(),
				$this->request->getPluginName()
			);
		}
		$temporaryContentObject = new ContentObjectRenderer();
		/** @var \TYPO3\CMS\Extbase\Mvc\Web\Request $request */
		$request = $this->objectManager->get($this->requestType);
		$request->setControllerActionName($action);
		$request->setControllerName($controller);
		$request->setPluginName($pluginName);
		$request->setControllerExtensionName($extensionName);
		$request->setArguments($arguments);
		// TODO: remove for 6.2 LTS
		if (FALSE === empty($vendorName)) {
			$request->setControllerVendorName($vendorName);
		}
		try {
			/** @var \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response */
			$response = $this->objectManager->get($this->responseType);
			$this->configurationManager->setContentObject($temporaryContentObject);
			$this->configurationManager->setConfiguration(
				$this->configurationManager->getConfiguration(
					ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
					$extensionName,
					$pluginName
				)
			);
			$this->dispatcher->dispatch($request, $response);
			$this->configurationManager->setContentObject($contentObjectBackup);
			if (TRUE === isset($configurationBackup)) {
				$this->configurationManager->setConfiguration($configurationBackup);
			}
			return $response;
		} catch (\Exception $error) {
			if (FALSE === (boolean) $this->arguments['graceful']) {
				throw $error;
			}
			if (FALSE === empty($this->arguments['onError'])) {
				return sprintf($this->arguments['onError'], array($error->getMessage()), $error->getCode());
			}
			return $error->getMessage() . ' (' . $error->getCode() . ')';
		}
	}

}
