<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Service;

use FluidTYPO3\Vhs\Asset;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AssetServiceTest
 */
class AssetServiceTest extends UnitTestCase {

	/**
	 * @dataProvider getBuildAllTestValues
	 * @param array $assets
	 * @param boolean $cached
	 * @param integer $expectedFiles
	 */
	public function testBuildAll(array $assets, $cached, $expectedFiles) {
		$GLOBALS['VhsAssets'] = $assets;
		$GLOBALS['TSFE'] = (object) array('content' => 'content');
		$instance = $this->getMock('FluidTYPO3\\Vhs\\Service\\AssetService', array('writeFile'));
		$instance->expects($this->exactly($expectedFiles))->method('writeFile')->with($this->anything(), $this->anything());
		if (TRUE === $cached) {
			$instance->buildAll(array(), $this, $cached);
		} else {
			$instance->buildAllUncached(array(), $this);
		}
		unset($GLOBALS['VhsAssets']);
	}

	/**
	 * @return array
	 */
	public function getBuildAllTestValues() {
		/** @var Asset $asset1 */
		$asset1 = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get('FluidTYPO3\\Vhs\\Asset');
		$asset1->setContent('asset');
		$asset1->setName('asset1');
		$asset1->setType('js');
		$asset2 = clone $asset1;
		$asset2->setName('asset2');
		$asset2->setType('css');
		$asset3 = clone $asset1;
		$asset3->setName('asset3');
		$asset3->setType('css');
		$asset3standalone = clone $asset3;
		$asset3standalone->setName('asset3standalone');
		$asset3standalone->setStandalone(TRUE);
		$fluidAsset = clone $asset1;
		$fluidAsset->setName('fluid');
		$fluidAsset->setFluid(TRUE);
		return array(
			array(array(), TRUE, 0, array()),
			array(array(), FALSE, 0, array()),
			array(array('asset1' => $asset1), TRUE, 1),
			array(array('asset1' => $asset1, 'asset2' => $asset2), TRUE, 2),
			array(array('asset1' => $asset1, 'asset2' => $asset2, 'asset3' => $asset3), TRUE, 2),
			array(array('asset1' => $asset1, 'asset2' => $asset2, 'asset3standalone' => $asset3standalone), TRUE, 2),
			array(array('fluid' => $fluidAsset), TRUE, 1)
		);
	}

}
