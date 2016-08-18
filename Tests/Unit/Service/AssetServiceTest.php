<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Service;

use FluidTYPO3\Vhs\Asset;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AssetServiceTest
 */
class AssetServiceTest extends UnitTestCase
{

    /**
     * @dataProvider getBuildAllTestValues
     * @param array $assets
     * @param boolean $cached
     * @param integer $expectedFiles
     */
    public function testBuildAll(array $assets, $cached, $expectedFiles)
    {
        $GLOBALS['VhsAssets'] = $assets;
        $GLOBALS['TSFE'] = (object) array('content' => 'content');
        $instance = $this->getMock('FluidTYPO3\\Vhs\\Service\\AssetService', array('writeFile'));
        $instance->expects($this->exactly($expectedFiles))->method('writeFile')->with($this->anything(), $this->anything());
        if (true === $cached) {
            $instance->buildAll(array(), $this, $cached);
        } else {
            $instance->buildAllUncached(array(), $this);
        }
        unset($GLOBALS['VhsAssets']);
    }

    /**
     * @return array
     */
    public function getBuildAllTestValues()
    {
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
        $asset3standalone->setStandalone(true);
        $fluidAsset = clone $asset1;
        $fluidAsset->setName('fluid');
        $fluidAsset->setFluid(true);
        return array(
            array(array(), true, 0, array()),
            array(array(), false, 0, array()),
            array(array('asset1' => $asset1), true, 1),
            array(array('asset1' => $asset1, 'asset2' => $asset2), true, 2),
            array(array('asset1' => $asset1, 'asset2' => $asset2, 'asset3' => $asset3), true, 2),
            array(array('asset1' => $asset1, 'asset2' => $asset2, 'asset3standalone' => $asset3standalone), true, 2),
            array(array('fluid' => $fluidAsset), true, 1)
        );
    }

    /**
     * @test
     */
    public function testIntegrityCalculation()
    {
        // Note: Maybe test this dynamic. This command could be useful:
        //    ~> openssl dgst -sha256 -binary Tests/Fixtures/Files/dummy.js | openssl base64 -A

        $expectedIntegrities = array(
           'sha256' => 'sha256-DUTqIDSUj1HagrQbSjhJtiykfXxVQ74BanobipgodCo=',
           'sha384' => 'sha384-aieE32yQSOy7uEhUkUvR9bVgfJgMsP+B9TthbxbjDDZ2hd4tjV5jMUoj9P8aeSHI',
           'sha512' => 'sha512-0bz2YVKEoytikWIUFpo6lK/k2cVVngypgaItFoRvNfux/temtdCVxsu+HxmdRT8aNOeJxxREUphbkcAK8KpkWg==',
        );

        if(!array_key_exists(Asset::INTEGRITY_METHOD, $expectedIntegrities)) {
            $this->markTestSkipped('There is no expectation for hashing method \'' . Asset::INTEGRITY_METHOD . '\'');
            return;
        }

        $expectedIntegrity = $expectedIntegrities[Asset::INTEGRITY_METHOD];
        $file = 'Tests/Fixtures/Files/dummy.js';
        $method = (new \ReflectionClass('\FluidTYPO3\Vhs\Service\AssetService'))->getMethod('getFileIntegrity');
        $instance = $this->getMock('FluidTYPO3\\Vhs\\Service\\AssetService', array('writeFile'));
        $instance->method('writeFile')->willReturn(null);

        $method->setAccessible(true);
        $this->assertEquals($expectedIntegrity, $method->invokeArgs($instance, array($file)));
    }
}
