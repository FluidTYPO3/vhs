<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use TYPO3\CMS\Core\Package\FailsafePackageManager;
use TYPO3\CMS\Core\Package\Package;
use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Package\PackageInterface;

/**
 * Class DummyPackageManager
 */
class DummyPackageManager extends FailsafePackageManager {

	/**
	 * @var array
	 */
	protected $packageStatesConfiguration = array(
		'packages' => array()
	);

	/**
	 * @param Bootstrap $bootstrap
	 */
	public function initialize(Bootstrap $bootstrap) {
		$this->bootstrap = $bootstrap;
	}

	/**
	 * @param string $packageKey
	 * @return boolean
	 */
	public function isPackageActive($packageKey) {
		return ('vhs' === $packageKey);
	}

	/**
	 * @param string $packageKey
	 * @return boolean
	 */
	public function isPackageAvailable($packageKey) {
		return ('vhs' === $packageKey);
	}

	/**
	 * @param string $packageKey
	 * @return PackageInterface
	 */
	public function getPackage($packageKey) {
		$path = realpath(dirname(__FILE__) . '/../../..') . '/';
		$package = new Package($this, $packageKey, $path, $path . 'Classes/');
		return $package;
	}

}
