<?php
namespace FluidTYPO3\Vhs;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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

use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * @author Claus Due <claus@namelesscoder.net>
 * @package Vhs
 */
class AssetTest extends UnitTestCase {

	/**
	 * @test
	 */
	public function setsMovableFalseWhenSettingTypeCss() {
		$asset = Asset::getInstance();
		$asset->setMovable(TRUE);
		$asset->setType('css');
		$this->assertFalse($asset->getMovable());
	}

	/**
	 * @test
	 */
	public function canCreateAssetInstanceFromStaticFactory() {
		$asset = Asset::getInstance();
		$this->assertInstanceOf('FluidTYPO3\Vhs\Asset', $asset);
	}

	/**
	 * @test
	 */
	public function canCreateAssetInstanceFromStaticFileFactoryWithRelativeFileAndTranslatesRelativeToAbsolutePath() {
		$file = $this->getRelativeAssetFixturePath();
		$asset = Asset::createFromFile($file);
		$this->assertInstanceOf('FluidTYPO3\Vhs\Asset', $asset);
		$this->assertEquals(GeneralUtility::getFileAbsFileName($file), $asset->getPath());
	}

	/**
	 * @test
	 */
	public function canCreateAssetInstanceFromStaticFileFactoryWithAbsoluteFile() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Asset::createFromFile($file);
		$this->assertInstanceOf('FluidTYPO3\Vhs\Asset', $asset);
		$this->assertEquals($file, $asset->getPath());
	}

	/**
	 * @test
	 */
	public function canCreateAssetInstanceFromStaticFileFactoryWithUrl() {
		$url = 'http://localhost';
		$asset = Asset::createFromUrl($url);
		$this->assertInstanceOf('FluidTYPO3\Vhs\Asset', $asset);
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
		$asset = Asset::createFromSettings($settings);
		$this->assertInstanceOf('FluidTYPO3\Vhs\Asset', $asset);
	}

	/**
	 * @test
	 */
	public function supportsChainingInAllSettersWithFakeNullArgument() {
		$asset = Asset::getInstance();
		$settableProperties = ObjectAccess::getSettablePropertyNames($asset);
		foreach ($settableProperties as $propertyName) {
			$setter = 'set' . ucfirst($propertyName);
			$asset = $asset->$setter(NULL);
			$this->assertInstanceOf('FluidTYPO3\Vhs\Asset', $asset, 'The ' . $setter . ' method does not support chaining');
		}
	}

	/**
	 * @test
	 */
	public function assetsCanBeAdded() {
		$name = 'dummy';
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Asset::createFromFile($file);
		$this->assertSame($asset, $GLOBALS['VhsAssets'][$name]);
	}

	/**
	 * @test
	 */
	public function assetCanBeRemoved() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Asset::createFromFile($file);
		$asset->remove();
		$this->assertSame(TRUE, $asset->getRemoved());
		$this->assertSame(TRUE, $asset->assertHasBeenRemoved());
		$constraint = new \PHPUnit_Framework_Constraint_IsType('array');
		$this->assertThat($asset->getSettings(), $constraint);
	}

	/**
	 * @test
	 */
	public function assetsAddedByFilenameUsesFileBasenameAsAssetName() {
		$file = $this->getAbsoluteAssetFixturePath();
		$expectedName = pathinfo($file, PATHINFO_FILENAME);
		$asset = Asset::createFromFile($file);
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
		$asset = Asset::createFromFile($file);
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
		$asset = Asset::createFromFile($file);
		$expectedTrimmedContent = trim(file_get_contents($file));
		$this->assertEquals($expectedTrimmedContent, trim($asset->getContent()));
	}

	/**
	 * @test
	 */
	public function specialGettersAndAssertionsReturnBooleans() {
		$file = $this->getAbsoluteAssetFixturePath();
		$asset = Asset::createFromFile($file);
		$constraint = new \PHPUnit_Framework_Constraint_IsType('boolean');
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
		$asset = Asset::createFromFile($file);
		$gettableProperties = ObjectAccess::getGettablePropertyNames($asset);
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		foreach ($gettableProperties as $propertyName) {
			if (FALSE === property_exists('FluidTYPO3\Vhs\Asset', $propertyName)) {
				continue;
			}
			$propertyValue = ObjectAccess::getProperty($asset, $propertyName);
			/** @var \TYPO3\CMS\Extbase\Reflection\PropertyReflection $propertyReflection */
			$propertyReflection = $objectManager->get('TYPO3\CMS\Extbase\Reflection\PropertyReflection', 'FluidTYPO3\Vhs\Asset', $propertyName);
			$expectedDataType = array_pop($propertyReflection->getTagValues('var'));
			$constraint = new \PHPUnit_Framework_Constraint_IsType($expectedDataType);
			$this->assertThat($propertyValue, $constraint);
		}
		$constraint = new \PHPUnit_Framework_Constraint_IsType('array');
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
		$asset = Asset::createFromFile($file);
		$constraint = new \PHPUnit_Framework_Constraint_IsType('string');
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
		$asset = Asset::createFromContent($content);
		$this->assertSame($content, $asset->getContent());
	}

	/**
	 * @return string
	 */
	protected function getRelativeAssetFixturePath() {
		$file = ExtensionManagementUtility::siteRelPath('vhs') . 'Tests/Fixtures/Files/dummy.js';
		return $file;
	}

	/**
	 * @return string
	 */
	protected function getAbsoluteAssetFixturePath() {
		$file = ExtensionManagementUtility::extPath('vhs', 'Tests/Fixtures/Files/dummy.js');
		return $file;
	}

}
