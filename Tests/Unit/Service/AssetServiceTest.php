<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Service;

use FluidTYPO3\Vhs\Asset;
use FluidTYPO3\Vhs\Service\AssetService;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\ConsumableNonce;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Directive;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class AssetServiceTest
 */
class AssetServiceTest extends AbstractTestCase
{
    public string $content = '';

    private ?ConfigurationManagerInterface $configurationManager = null;

    protected function setUp(): void
    {
        $this->configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
            ->getMockForAbstractClass();
        $this->configurationManager->method('getConfiguration')->willReturn([]);
        $this->singletonInstances[ConfigurationManagerInterface::class] = $this->configurationManager;

        // Required for TYPO3v10
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLocale'] = 'en_US';

        $this->resetAssetServiceSettingsCache();

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
        $request = new ServerRequest('https://example.local');
        $assets = $this->buildAssets($assets);

        $GLOBALS['VhsAssets'] = $assets;
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
            $instance->buildAll([], $request, $cached);
        } else {
            $instance->buildAllUncached([], $request);
        }
        unset($GLOBALS['VhsAssets']);
    }

    /**
     * @return array
     */
    public static function getBuildAllTestValues(): array
    {
        $asset1 = ['content' => 'asset', 'name' => 'asset1', 'type' => 'js'];
        $asset2 = ['content' => 'asset', 'name' => 'asset2', 'type' => 'css'];
        $asset3 = ['content' => 'asset', 'name' => 'asset3', 'type' => 'css'];
        $asset3standalone = ['content' => 'asset', 'name' => 'asset3standalone', 'type' => 'css', 'standalone' => true];
        $fluidAsset = ['content' => 'asset', 'name' => 'fluid', 'type' => 'js', 'fluid' => true];
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

    private function buildAssets(array $assetSettings): array
    {
        $assets = [];
        foreach ($assetSettings as $name => $settings) {
            $asset = new Asset();
            foreach ($settings as $property => $value) {
                $asset->{'set' . ucfirst($property)}($value);
            }
            $assets[$name] = $asset;
        }
        return $assets;
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

        // This represents the setting levels, from 0=off over 1 as the weakest to 3 as the strongest
        $expectedIntegrities = [
           '', // This makes sense, cause on 0, the generation should be disabled
           'sha256-DUTqIDSUj1HagrQbSjhJtiykfXxVQ74BanobipgodCo=',
           'sha384-aieE32yQSOy7uEhUkUvR9bVgfJgMsP+B9TthbxbjDDZ2hd4tjV5jMUoj9P8aeSHI',
           'sha512-0bz2YVKEoytikWIUFpo6lK/k2cVVngypgaItFoRvNfux/temtdCVxsu+HxmdRT8aNOeJxxREUphbkcAK8KpkWg==',
        ];

        $file = 'Tests/Fixtures/Files/dummy.js';
        $request = new ServerRequest('https://example.local');

        foreach ($expectedIntegrities as $settingLevel => $expectedIntegrity) {
            $method = (new \ReflectionClass(AssetService::class))->getMethod('getFileIntegrity');
            $instance = $this->getMockBuilder(AssetService::class)
                ->onlyMethods(['writeFile', 'getTypoScript'])
                ->getMock();
            $instance->method('getTypoScript')->willReturn(
                [
                    'assets' => [
                        'tagsAddSubresourceIntegrity' => $settingLevel,
                    ],
                ]
            );
            $method->setAccessible(true);
            $this->assertEquals($expectedIntegrity, $method->invokeArgs($instance, [$file, $request]));
        }
    }

    /**
     * @test
     */
    public function getSettingsKeepsRuntimeCacheSeparatedByRequestPageContext()
    {
        $request1 = (new ServerRequest('https://example.local/page-1'))
            ->withAttribute('routing', new PageArguments(1, '0', []));
        $request2 = (new ServerRequest('https://example.local/page-2'))
            ->withAttribute('routing', new PageArguments(2, '0', []));

        $instance = $this->getMockBuilder(AssetService::class)
            ->onlyMethods(['getTypoScript'])
            ->getMock();
        $instance->expects($this->exactly(2))
            ->method('getTypoScript')
            ->willReturnOnConsecutiveCalls(
                ['settings' => ['request' => 'first']],
                ['settings' => ['request' => 'second']]
            );

        $this->assertSame(['request' => 'first'], $instance->getSettings($request1));
        $this->assertSame(['request' => 'second'], $instance->getSettings($request2));
        $this->assertSame(['request' => 'first'], $instance->getSettings($request1));
    }

    /**
     * @test
     */
    public function enableFooterRelocationSettingWithoutRelocateToFooterKeepsAssetsInFooter()
    {
        $request = new ServerRequest('https://example.local');
        $content = '<html><head></head><body></body></html>';
        $assets = [
            'footerAsset' => [
                'type' => 'js',
                'movable' => true,
                'variables' => [],
            ],
        ];

        $instance = $this->getMockBuilder(AssetService::class)
            ->onlyMethods(['buildAssetsChunk', 'getSettings'])
            ->getMock();
        $instance->method('getSettings')->willReturn(['enableFooterRelocation' => 1]);
        $instance->method('buildAssetsChunk')->willReturnCallback(
            static function (array $assets): string {
                return isset($assets['footerAsset']) ? 'footer-asset' : '';
            }
        );

        set_error_handler(
            static function (int $severity, string $message, string $file, int $line): bool {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            }
        );
        $method = (new \ReflectionClass(AssetService::class))->getMethod('placeAssetsInHeaderAndFooter');
        $method->setAccessible(true);
        try {
            $method->invokeArgs($instance, [$assets, true, &$content, $request]);
        } finally {
            restore_error_handler();
        }

        $this->assertStringContainsString('footer-asset</body>', $content);
    }

    /**
     * @test
     */
    public function inlineJavaScriptTagConsumesCspNonceWhenEnabled()
    {
        [$tag, $nonce] = $this->generateAssetTagWithNonce(
            'js',
            'alert(1);',
            null,
            ['csp' => true, 'async' => false, 'defer' => false]
        );

        $this->assertStringContainsString('nonce="' . $nonce->value . '"', $tag);
        $this->assertSame(1, $nonce->countInline(Directive::ScriptSrcElem));
        $this->assertSame(0, $nonce->countStatic(Directive::ScriptSrcElem));
    }

    /**
     * @test
     */
    public function inlineStyleTagConsumesCspNonceWhenEnabled()
    {
        [$tag, $nonce] = $this->generateAssetTagWithNonce('css', 'body { color: #000; }', null, ['csp' => true]);

        $this->assertStringContainsString('nonce="' . $nonce->value . '"', $tag);
        $this->assertSame(1, $nonce->countInline(Directive::StyleSrcElem));
        $this->assertSame(0, $nonce->countStatic(Directive::StyleSrcElem));
    }

    /**
     * @test
     */
    public function inlineTagsDoNotConsumeCspNonceByDefault()
    {
        [$tag, $nonce] = $this->generateAssetTagWithNonce('js', 'alert(1);');

        $this->assertStringNotContainsString('nonce=', $tag);
        $this->assertSame(0, $nonce->countInline(Directive::ScriptSrcElem));
        $this->assertSame(0, $nonce->countStatic(Directive::ScriptSrcElem));
    }

    /**
     * @test
     */
    public function fileTagsConsumeStaticCspNonceByDefault()
    {
        [$tag, $nonce] = $this->generateAssetTagWithNonce('js', null, 'fileadmin/test.js');

        $this->assertStringContainsString('nonce="' . $nonce->value . '"', $tag);
        $this->assertSame(0, $nonce->countInline(Directive::ScriptSrcElem));
        $this->assertSame(1, $nonce->countStatic(Directive::ScriptSrcElem));
    }

    /**
     * @test
     */
    public function fluidAssetRenderingReceivesActiveRequest()
    {
        $activeRequest = new ServerRequest('https://inner.example/request-222');
        $GLOBALS['TYPO3_REQUEST'] = new ServerRequest('https://outer.example/request-111');

        $asset = new Asset();
        $asset->setName('fluid');
        $asset->setType('js');
        $asset->setStandalone(false);
        $asset->setExternal(true);
        $asset->setFluid(true);
        $asset->setPath(__DIR__ . '/../../Fixtures/Files/dummy.js');
        $GLOBALS['VhsAssets'] = ['fluid' => $asset];

        $instance = $this->getMockBuilder(AssetService::class)
            ->onlyMethods(
                [
                    'getSettings',
                    'getTypoScript',
                    'readCacheDisabledInstructionFromContext',
                    'renderAssetAsFluidTemplate',
                    'resolveAbsolutePathForFile',
                    'writeFile',
                ]
            )
            ->getMock();
        $instance->method('getSettings')->willReturn([]);
        $instance->method('getTypoScript')->willReturn([]);
        $instance->method('readCacheDisabledInstructionFromContext')->willReturn(false);
        $instance->method('resolveAbsolutePathForFile')->willReturnArgument(0);
        $instance->expects($this->once())
            ->method('renderAssetAsFluidTemplate')
            ->with($this->identicalTo($asset), $this->identicalTo($activeRequest))
            ->willReturn('alert("active request");');

        $content = '<html><head></head><body></body></html>';
        try {
            $instance->buildAll([], $activeRequest, true, $content);
        } finally {
            unset($GLOBALS['VhsAssets'], $GLOBALS['TYPO3_REQUEST']);
        }
    }

    private function resetAssetServiceSettingsCache(): void
    {
        $property = new \ReflectionProperty(AssetService::class, 'settingsCache');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    private function generateAssetTagWithNonce(
        string $type,
        ?string $content,
        ?string $file = null,
        ?array $standaloneAssetSettings = null
    ): array {
        $nonce = new ConsumableNonce(str_repeat('a', 40));
        $request = (new ServerRequest('https://example.local'))->withAttribute('nonce', $nonce);
        $method = (new \ReflectionClass(AssetService::class))->getMethod('generateTagForAssetType');
        $method->setAccessible(true);

        $instance = $this->getMockBuilder(AssetService::class)
            ->onlyMethods(['getSettings', 'getTypoScript'])
            ->getMock();
        $instance->method('getSettings')->willReturn([]);
        $instance->method('getTypoScript')->willReturn([]);

        $tag = $method->invokeArgs(
            $instance,
            [$type, $content, $file, null, $standaloneAssetSettings, $request]
        );

        return [$tag, $nonce];
    }
}
