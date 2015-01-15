<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
		return array();
	}

	/**
	 * @param string $featureName
	 * @return boolean
	 */
	public function isFeatureEnabled($featureName) {
		TRUE;
	}

}
