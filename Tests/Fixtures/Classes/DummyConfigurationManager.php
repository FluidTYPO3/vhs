<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class DummyConfigurationManager
 */
class DummyConfigurationManager extends BackendConfigurationManager implements ConfigurationManagerInterface {

	/**
	 * @param string $type
	 * @param string $extensionName
	 * @param string $pluginName
	 * @return array
	 */
	public function getConfiguration($type, $extensionName = NULL, $pluginName = NULL) {
		return array(
			'config' => array(
				'tx_extbase' => array(
					'features' => array(
						'rewrittenPropertyMapper' => TRUE
					)
				)
			)
		);
	}

	/**
	 * @param string $featureName
	 * @return boolean
	 */
	public function isFeatureEnabled($featureName) {
		TRUE;
	}

	/**
	 * @return ContentObjectRenderer
	 */
	public function getContentObject() {
		return new ContentObjectRenderer();
	}

}
