<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Service;

use FluidTYPO3\Vhs\Asset;
use FluidTYPO3\Vhs\Service\AssetService;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class AssetServiceTest
 */
class AssetServiceTest extends AbstractTestCase
{
    private ?ConfigurationManagerInterface $configurationManager = null;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)->getMockForAbstractClass();
        GeneralUtility::setSingletonInstance(ConfigurationManagerInterface::class, $this->configurationManager);

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp(): void
    {
        $this->singletonInstances[ConfigurationManagerInterface::class] = $this->configurationManager;

        // Required for TYPO3v10
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLocale'] = 'en_US';

        parent::setUp();
    }

    /**
     * @dataProvider getBuildAllTestValues
     * @param array $assets
     * @param boolean $cached
     * @param integer $expectedFiles
     */
    public function testBuildAll(array $assets, $cached, $expectedFiles)
    {
        $GLOBALS['VhsAssets'] = $assets;
        $GLOBALS['TSFE'] = $this->getMockBuilder(TypoScriptFrontendController::class)
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TSFE']->content = 'content';
        $instance = $this->getMockBuilder(AssetService::class)
            ->onlyMethods(
                [
                    'writeFile',
                    'getSettings',
                    'resolveAbsolutePathForFile',
                    'getTypoScript',
                    'readCacheDisabledInstructionFromContext'
                ]
            )
            ->getMock();
        $instance->expects($this->exactly($expectedFiles))
            ->method('writeFile')
            ->with($this->anything(), $this->anything());
        $instance->method('getSettings')->willReturn([]);
        $instance->method('getTypoScript')->willReturn([]);
        $instance->method('resolveAbsolutePathForFile')->willReturnArgument(0);
        if (true === $cached) {
            $instance->buildAll([], $this, $cached);
        } else {
            $instance->buildAllUncached([], $this);
        }
        unset($GLOBALS['VhsAssets'], $GLOBALS['TSFE']);
    }

    /**
     * @return array
     */
    public function getBuildAllTestValues()
    {
        /** @var Asset $asset1 */
        $asset1 = new Asset();
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
        return [
            [[], true, 0, []],
            [[], false, 0, []],
            [['asset1' => $asset1], true, 1],
            [['asset1' => $asset1, 'asset2' => $asset2], true, 2],
            [['asset1' => $asset1, 'asset2' => $asset2, 'asset3' => $asset3], true, 2],
            [['asset1' => $asset1, 'asset2' => $asset2, 'asset3standalone' => $asset3standalone], true, 2],
            [['fluid' => $fluidAsset], true, 1]
        ];
    }

    /**
     * @test
     */
    public function testIntegrityCalculation()
    {
        // Note: Maybe test this dynamic. This command could be useful:
        //    ~> openssl dgst -sha256 -binary Tests/Fixtures/Files/dummy.js | openssl base64 -A

        if ((!extension_loaded('hash') || !function_exists('hash_algos'))
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

        foreach ($expectedIntegrities as $settingLevel => $expectedIntegrity) {
            $method = (new \ReflectionClass(AssetService::class))->getMethod('getFileIntegrity');
            $instance = $this->getMockBuilder(AssetService::class)->onlyMethods(['writeFile', 'getTypoScript'])->getMock();
            $instance->method('getTypoScript')->willReturn(
                [
                    'assets' => [
                        'tagsAddSubresourceIntegrity' => $settingLevel,
                    ],
                ]
            );
            $method->setAccessible(true);
            $this->assertEquals($expectedIntegrity, $method->invokeArgs($instance, [$file]));
        }
    }
}
