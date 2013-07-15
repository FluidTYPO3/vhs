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
	public function canCreateAssetInstanceFromStaticFileFactoryWithUrl() {
		$url = 'http://localhost';
		$asset = Tx_Vhs_Asset::createFromUrl($url);
		$this->assertInstanceOf('Tx_Vhs_Asset', $asset);
		$this->assertEquals($url, $asset->getPath());
		$this->assertSame(TRUE, $asset->getStandalone());
		$this->assertSame(TRUE, $asset->getExternal());
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
	 * @test
	 */
	public function assetsCanBeAdded() {
		$name = 'dummy';
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$this->assertSame($asset, $GLOBALS['VhsAssets'][$name]);
	}

	/**
	 * @test
	 */
	public function assetCanBeRemoved() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$asset->remove();
		$this->assertSame(TRUE, $asset->getRemoved());
		$this->assertSame(TRUE, $asset->assertHasBeenRemoved());
		$constraint = new PHPUnit_Framework_Constraint_IsType('array');
		$this->assertThat($asset->getSettings(), $constraint);
	}

	/**
	 * @test
	 */
	public function assetsAddedByFilenameUsesFileBasenameAsAssetName() {
		$file = $this->getAbsoluteAssetFixturePath();
		$expectedName = pathinfo($file, PATHINFO_FILENAME);
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$this->assertSame($asset, $GLOBALS['VhsAssets'][$expectedName]);
		$this->assertEquals(
			$expectedName, $asset->getName(),
			'Getter for name property does not return the expected name after creation from file path'
		);
	}

	/**
	 * @test
	 */
	public function assetBuildMethodReturnsExpectedContentComparedByTrimmedContent() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$expectedTrimmedContent = trim(file_get_contents($file));
		$this->assertEquals($expectedTrimmedContent, trim($asset->build()));
		$asset->setContent(file_get_contents($file));
		$asset->setPath(NULL);
		$this->assertEquals($expectedTrimmedContent, trim($asset->build()));
	}

	/**
	 * @test
	 */
	public function assetGetContentMethodReturnsExpectedContentComparedByTrimmedContent() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$expectedTrimmedContent = trim(file_get_contents($file));
		$this->assertEquals($expectedTrimmedContent, trim($asset->getContent()));
	}

	/**
	 * @test
	 */
	public function specialGettersAndAssertionsReturnBooleans() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$constraint = new PHPUnit_Framework_Constraint_IsType('boolean');
		$this->assertThat($asset->getRemoved(), $constraint);
		$this->assertThat($asset->assertAddNameCommentWithChunk(), $constraint);
		$this->assertThat($asset->assertAllowedInFooter(), $constraint);
		$this->assertThat($asset->assertDebugEnabled(), $constraint);
		$this->assertThat($asset->assertFluidEnabled(), $constraint);
		$this->assertThat($asset->assertHasBeenRemoved(), $constraint);
	}

	/**
	 * @test
	 */
	public function specialSupportGettersReturnExpectedTypes() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$gettableProperties = Tx_Extbase_Reflection_ObjectAccess::getGettablePropertyNames($asset);
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		foreach ($gettableProperties as $propertyName) {
			if (FALSE === property_exists('Tx_Vhs_Asset', $propertyName)) {
				continue;
			}
			$propertyValue = Tx_Extbase_Reflection_ObjectAccess::getProperty($asset, $propertyName);
			/** @var $propertyReflection Tx_Extbase_Reflection_PropertyReflection */
			$propertyReflection = $objectManager->get('Tx_Extbase_Reflection_PropertyReflection', 'Tx_Vhs_Asset', $propertyName);
			$expectedDataType = array_pop($propertyReflection->getTagValues('var'));
			$constraint = new PHPUnit_Framework_Constraint_IsType($expectedDataType);
			$this->assertThat($propertyValue, $constraint);
		}
		$constraint = new PHPUnit_Framework_Constraint_IsType('array');
		$this->assertThat($asset->getDebugInformation(), $constraint);
		$this->assertThat($asset->getAssetSettings(), $constraint);
		$this->assertGreaterThan(0, count($asset->getAssetSettings()));
		$this->assertThat($asset->getSettings(), $constraint);
		$this->assertGreaterThan(0, count($asset->getSettings()));
		$this->assertNotNull($asset->getContent());
	}

	/**
	 * @test
	 */
	public function buildMethodsReturnExpectedValues() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Tx_Vhs_Asset::createFromFile($file);
		$constraint = new PHPUnit_Framework_Constraint_IsType('string');
		$this->assertThat($asset->render(), $constraint);
		$this->assertNotEmpty($asset->render());
		$this->assertThat($asset->build(), $constraint);
		$this->assertNotEmpty($asset->build());
		$this->assertSame($asset, $asset->finalize());
	}

	/**
	 * @test
	 */
	public function assertSupportsRawContent() {
		$file = $this->getAbsoluteAssetFixturePath();
		$content = file_get_contents($file);
		$asset = Tx_Vhs_Asset::createFromContent($content);
		$this->assertSame($content, $asset->getContent());
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
