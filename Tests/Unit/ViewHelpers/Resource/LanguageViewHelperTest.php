<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\AccessibleExtensionManagementUtility;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyLocalizationFactory;

/**
 * Class LanguageViewHelperTest
 */
class LanguageViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->onlyMethods(['getPackagePath'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager = $this->getMockBuilder(PackageManager::class)
            ->onlyMethods(['getPackage', 'isPackageActive'])
            ->disableOriginalConstructor()
            ->getMock();
        $packageManager->method('getPackage')->willReturn($package);
        $packageManager->method('isPackageActive')->willReturn(true);
        AccessibleExtensionManagementUtility::setPackageManager($packageManager);

        $this->setClassAlias(LocalizationFactory::class, DummyLocalizationFactory::class);

        parent::setUp();
    }

    /**
     * @test
     */
    public function testRenderFailsWhenUnableToResolveExtensionName(): void
    {
        $language = $this->getMockBuilder(SiteLanguage::class)
            ->onlyMethods(['getLocale'])
            ->disableOriginalConstructor()
            ->getMock();
        $language->method('getLocale')->willReturn(new Locale());

        $GLOBALS['TYPO3_REQUEST'] = $this->getMockBuilder(ServerRequest::class)
            ->addMethods(['dummy'])
            ->disableOriginalConstructor()
            ->getMock();
        $GLOBALS['TYPO3_REQUEST'] = $GLOBALS['TYPO3_REQUEST']
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('language', $language);
        $this->renderingContext = $this->createRenderingContextWithRequest($GLOBALS['TYPO3_REQUEST']);

        $output = $this->executeViewHelper();
        self::assertSame([], $output);
    }

    /**
     * @test
     */
    public function initializedLanguageFallsBackToDefaultWithoutRenderingContextRequest(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $viewHelper = $this->createInstance();
        $viewHelper->setRenderingContext($this->createRenderingContextWithoutRequest());

        self::assertSame('default', $this->callInaccessibleMethod($viewHelper, 'getInitializedLanguage'));
    }

    /**
     * @test
     */
    public function usesRenderingContextRequestWhenResolvingInitializedLanguage(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('language', $this->createSiteLanguageWithLocale('en-US'));
        $subRequest = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('language', $this->createSiteLanguageWithLocale('de-DE'));

        $viewHelper = $this->createInstance();
        $viewHelper->setRenderingContext($this->createRenderingContextWithRequest($subRequest));

        self::assertSame('de', $this->callInaccessibleMethod($viewHelper, 'getInitializedLanguage'));
    }

    private function createSiteLanguageWithLocale(string $locale): SiteLanguage
    {
        $language = $this->getMockBuilder(SiteLanguage::class)
            ->onlyMethods(['getLocale'])
            ->disableOriginalConstructor()
            ->getMock();
        $language->method('getLocale')->willReturn(new Locale($locale));
        return $language;
    }
}
