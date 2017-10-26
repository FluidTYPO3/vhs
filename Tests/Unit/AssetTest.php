<?php
namespace FluidTYPO3\Vhs\Tests\Unit;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Development\AbstractTestCase;
use FluidTYPO3\Vhs\Asset;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Reflection\PropertyReflection;

/**
 * Class AssetTest
 */
class AssetTest extends AbstractTestCase
{

    /**
     * @return void
     */
    public function setUp()
    {
        $GLOBALS['VhsAssets'] = [];
    }

    /**
     * @test
     */
    public function setsMovableFalseWhenSettingTypeCss()
    {
        $asset = Asset::getInstance();
        $asset->setMovable(true);
        $asset->setType('css');
        $this->assertFalse($asset->getMovable());
    }

    /**
     * @test
     */
    public function canCreateAssetInstanceFromStaticFactory()
    {
        $asset = Asset::getInstance();
        $this->assertInstanceOf(Asset::class, $asset);
    }

    /**
     * @test
     */
    public function canCreateAssetInstanceFromStaticFileFactoryWithRelativeFileAndTranslatesRelativeToAbsolutePath()
    {
        $file = 'Tests/Fixtures/Files/dummy.js';
        $expected = $this->getAbsoluteAssetFixturePath();
        $asset = Asset::createFromFile($file);
        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertStringEndsWith($file, $asset->getPath());
        $this->assertNotEquals($file, $asset->getPath());
    }

    /**
     * @test
     */
    public function canCreateAssetInstanceFromStaticFileFactoryWithAbsoluteFile()
    {
        $file = $this->getAbsoluteAssetFixturePath();
        $asset = Asset::createFromFile($file);
        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertEquals($file, $asset->getPath());
    }

    /**
     * @test
     */
    public function canCreateAssetInstanceFromStaticFileFactoryWithUrl()
    {
        $url = 'http://localhost';
        $asset = Asset::createFromUrl($url);
        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertEquals($url, $asset->getPath());
        $this->assertSame(true, $asset->getStandalone());
        $this->assertSame(true, $asset->getExternal());
    }

    /**
     * @test
     */
    public function canCreateAssetInstanceFromStaticSettingsFactory()
    {
        $file = $this->getAbsoluteAssetFixturePath();
        $settings = [
            'file' => $file
        ];
        $asset = Asset::createFromSettings($settings);
        $this->assertInstanceOf(Asset::class, $asset);
    }

    /**
     * @test
     */
    public function supportsChainingInAllSettersWithFakeNullArgument()
    {
        $asset = Asset::getInstance();
        $settableProperties = ObjectAccess::getSettablePropertyNames($asset);
        foreach ($settableProperties as $propertyName) {
            $setter = 'set' . ucfirst($propertyName);
            $asset = $asset->$setter(null);
            $this->assertInstanceOf(Asset::class, $asset, 'The ' . $setter . ' method does not support chaining');
        }
    }

    /**
     * @test
     */
    public function assetsCanBeAdded()
    {
        $file = $this->getAbsoluteAssetFixturePath();
        $asset = Asset::createFromFile($file);
        $name = $asset->getName();
        $this->assertSame($asset, $GLOBALS['VhsAssets'][$name]);
    }

    /**
     * @test
     */
    public function assetCanBeRemoved()
    {
        $file = $this->getAbsoluteAssetFixturePath();
        $asset = Asset::createFromFile($file);
        $asset->remove();
        $this->assertSame(true, $asset->getRemoved());
        $this->assertSame(true, $asset->assertHasBeenRemoved());
        $constraint = new \PHPUnit_Framework_Constraint_IsType('array');
        $this->assertThat($asset->getSettings(), $constraint);
    }

    /**
     * @test
     */
    public function assetsAddedByFilenameUsesFileBasenameAsAssetName()
    {
        $file = $this->getAbsoluteAssetFixturePath();
        $expectedName = pathinfo($file, PATHINFO_FILENAME);
        $asset = Asset::createFromFile($file);
        $this->assertSame($asset, $GLOBALS['VhsAssets'][$expectedName]);
        $this->assertEquals(
            $expectedName,
            $asset->getName(),
            'Getter for name property does not return the expected name after creation from file path'
        );
    }

    /**
     * @test
     */
    public function assetBuildMethodReturnsExpectedContentComparedByTrimmedContent()
    {
        $file = $this->getAbsoluteAssetFixturePath();
        $asset = Asset::createFromFile($file);
        $expectedTrimmedContent = trim(file_get_contents($file));
        $this->assertEquals($expectedTrimmedContent, trim($asset->build()));
        $asset->setContent(file_get_contents($file));
        $asset->setPath(null);
        $this->assertEquals($expectedTrimmedContent, trim($asset->build()));
    }

    /**
     * @test
     */
    public function assetGetContentMethodReturnsExpectedContentComparedByTrimmedContent()
    {
        $file = $this->getAbsoluteAssetFixturePath();
        $asset = Asset::createFromFile($file);
        $expectedTrimmedContent = trim(file_get_contents($file));
        $this->assertEquals($expectedTrimmedContent, trim($asset->getContent()));
    }

    /**
     * @test
     */
    public function specialGettersAndAssertionsReturnBooleans()
    {
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
    public function specialSupportGettersReturnExpectedTypes()
    {
        $file = $this->getAbsoluteAssetFixturePath();
        $asset = Asset::createFromFile($file);
        $gettableProperties = ObjectAccess::getGettablePropertyNames($asset);
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        foreach ($gettableProperties as $propertyName) {
            if (false === property_exists(Asset::class, $propertyName)) {
                continue;
            }
            $propertyValue = ObjectAccess::getProperty($asset, $propertyName);
            /** @var \ReflectionProperty $propertyReflection */
            $propertyReflection = $objectManager->get(\ReflectionProperty::class, Asset::class, $propertyName);
            $docComment = $propertyReflection->getDocComment();
            $matches = [];
            preg_match('/@var ([a-z\\\\0-9_]+)/i', $docComment, $matches);
            $expectedDataType = $matches[1];
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
    public function buildMethodsReturnExpectedValues()
    {
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
    public function assertSupportsRawContent()
    {
        $file = $this->getAbsoluteAssetFixturePath();
        $content = file_get_contents($file);
        $asset = Asset::createFromContent($content);
        $this->assertSame($content, $asset->getContent());
    }

    /**
     * @return string
     */
    protected function getRelativeAssetFixturePath()
    {
        $file = ExtensionManagementUtility::siteRelPath('vhs') . 'Tests/Fixtures/Files/dummy.js';
        return $file;
    }

    /**
     * @return string
     */
    protected function getAbsoluteAssetFixturePath()
    {
        $file = ExtensionManagementUtility::extPath('vhs', 'Tests/Fixtures/Files/dummy.js');
        return $file;
    }
}
