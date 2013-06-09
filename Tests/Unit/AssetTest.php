<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>, Wildside A/S
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Vhs
 */
class Tx_Vhs_AssetTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @test
	 */
	public function canCreateAssetInstanceFromStaticFactory() {
		$asset = Tx_Vhs_Asset::getInstance();
		$this->assertInstanceOf('Tx_Vhs_Asset', $asset);
	}

	/**
	 * @test
	 */
	public function canCreateAssetInstanceFromStaticFileFactoryWithRelativeFileAndTranslatesRelativeToAbsolutePath() {
		$file = $this->getRelativeAssetFixturePath();
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$this->assertInstanceOf('Tx_Vhs_Asset', $asset);
		$this->assertEquals(t3lib_div::getFileAbsFileName($file), $asset->getPath());
	}

	/**
	 * @test
	 */
	public function canCreateAssetInstanceFromStaticFileFactoryWithAbsoluteFile() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$this->assertInstanceOf('Tx_Vhs_Asset', $asset);
		$this->assertEquals($file, $asset->getPath());
	}

	/**
	 * @test
	 */
	public function canCreateAssetInstanceFromStaticSettingsFactory() {
		$file = $this->getAbsoluteAssetFixturePath();
		$settings = array(
			'file' => $file
		);
		$asset = Tx_Vhs_Asset::createFromSettings($settings);
		$this->assertInstanceOf('Tx_Vhs_Asset', $asset);
	}

	/**
	 * @test
	 */
	public function supportsChainingInAllSettersWithFakeNullArgument() {
		$asset = Tx_Vhs_Asset::getInstance();
		$settableProperties = Tx_Extbase_Reflection_ObjectAccess::getSettablePropertyNames($asset);
		foreach ($settableProperties as $propertyName) {
			$setter = 'set' . ucfirst($propertyName);
			$asset = $asset->$setter(NULL);
			$this->assertInstanceOf('Tx_Vhs_Asset', $asset, 'The ' . $setter . ' method does not support chaining');
		}
	}

	/**
	 * @return string
	 */
	protected function getRelativeAssetFixturePath() {
		$file = t3lib_extMgm::siteRelPath('vhs') . 'Tests/Fixtures/Files/dummy.js';
		return $file;
	}

	/**
	 * @return string
	 */
	protected function getAbsoluteAssetFixturePath() {
		$file = t3lib_extMgm::extPath('vhs', 'Tests/Fixtures/Files/dummy.js');
		return $file;
	}

}
