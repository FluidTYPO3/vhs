<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyQueryBuilder;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Condition\Page\IsLanguageViewHelper;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class IsLanguageViewHelperTest
 */
class IsLanguageViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $language = new LanguageAspect(123);

        $context = $this->getMockBuilder(Context::class)
            ->onlyMethods(['getAspect'])
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('getAspect')->with('language')->willReturn($language);

        $this->singletonInstances[Context::class] = $context;

        parent::setUp();
    }

    public function testWithLanguageAsStringLocale(): void
    {
        $queryBuilder = new DummyQueryBuilder($this);
        if (version_compare(VersionNumberUtility::getCurrentTypo3Version(), '10.4', '>=')) {
            $queryBuilder->result->method('fetchAssociative')->willReturn(['uid' => 123]);
        } else {
            $queryBuilder->result->method('fetch')->willReturn(['uid' => 123]);
        }

        $connectionPool = $this->getMockBuilder(ConnectionPool::class)
            ->onlyMethods(['getQueryBuilderForTable'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionPool->method('getQueryBuilderForTable')->willReturn($queryBuilder);

        GeneralUtility::addInstance(ConnectionPool::class, $connectionPool);

        $renderingContext = $this->getMockBuilder(RenderingContextInterface::class)->getMock();
        self::assertTrue(
            IsLanguageViewHelper::verdict(['language' => 'en', 'defaultTitle' => 'en'], $renderingContext)
        );
    }

    public function testWithLanguageAsUid(): void
    {
        $renderingContext = $this->getMockBuilder(RenderingContextInterface::class)->getMock();
        self::assertTrue(
            IsLanguageViewHelper::verdict(['language' => 123, 'defaultTitle' => 'en'], $renderingContext)
        );
    }
}
