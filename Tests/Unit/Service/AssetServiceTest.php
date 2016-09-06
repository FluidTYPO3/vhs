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

        if((!extension_loaded('hash') || !function_exists('hash_algos'))
            && (!extension_loaded('openssl') || !function_exists('openssl_get_md_methods'))
        ) {
            $this->markTestSkipped('No hash or openssl support');
        }

        $GLOBALS['TSFE'] = unserialize('O:8:"stdClass":1:{s:4:"tmpl";O:8:"stdClass":1:{s:5:"setup";a:1:{s:7:"plugin.";a:1:{s:7:"tx_vhs.";a:1:{s:7:"assets.";a:0:{}}}}}}');

        // This represents the setting levels, from 0=off over 1 as the weakest to 3 as the strongest
        $expectedIntegrities = [
           '', // This makes sense, cause on 0, the generation should be disabled
           'sha256-DUTqIDSUj1HagrQbSjhJtiykfXxVQ74BanobipgodCo=',
           'sha384-aieE32yQSOy7uEhUkUvR9bVgfJgMsP+B9TthbxbjDDZ2hd4tjV5jMUoj9P8aeSHI',
           'sha512-0bz2YVKEoytikWIUFpo6lK/k2cVVngypgaItFoRvNfux/temtdCVxsu+HxmdRT8aNOeJxxREUphbkcAK8KpkWg==',
        ];
        $file = 'Tests/Fixtures/Files/dummy.js';
        $method = (new \ReflectionClass('\FluidTYPO3\Vhs\Service\AssetService'))->getMethod('getFileIntegrity');
        $instance = $this->getMock('FluidTYPO3\\Vhs\\Service\\AssetService', array('writeFile'));
        $instance->method('writeFile')->willReturn(null);

        $method->setAccessible(true);
        foreach($expectedIntegrities as $settingLevel => $expectedIntegrity) {
            $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_vhs.']['assets.']['tagsAddSubresourceIntegrity'] = $settingLevel;
            $this->assertEquals($expectedIntegrity, $method->invokeArgs($instance, array($file)));
        }

        unset($GLOBALS['TSFE']);
    }
}
