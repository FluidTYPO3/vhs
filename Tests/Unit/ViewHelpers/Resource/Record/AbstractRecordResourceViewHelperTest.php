<?php

namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Classes\DummyQueryBuilder;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Resource\Record\AbstractRecordResourceViewHelper;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AbstractRecordResourceViewHelperTest extends AbstractTestCase
{
    private ?AbstractRecordResourceViewHelper $subject = null;

    protected function setUp(): void
    {
        $this->subject = $this->getMockBuilder(AbstractRecordResourceViewHelper::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        parent::setUp();
    }

    public function testGetResourcesReturnsEmptyArrayOnEmptyRecordField(): void
    {
        $this->subject->setArguments(['field' => 'field']);
        self::assertSame([], $this->subject->getResources(['field' => '']));
    }

    public function testGetRecord(): void
    {
        $record = ['uid' => 1];

        $mockQueryBuilder = new DummyQueryBuilder($this);
        $mockQueryBuilder->result->method('fetchAssociative')->willReturn($record);

        $arguments = ['table' => 'table'];

        $this->subject->setArguments($arguments);

        self::assertSame($record, $this->subject->getRecord(1));
    }

    public function testGetRecordWithFrontendPreview(): void
    {
        $record = ['uid' => 1];

        $context = $this->getMockBuilder(Context::class)
            ->setMethods(['hasAspect', 'getPropertyFromAspect'])
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('hasAspect')->willReturn(true);
        $context->method('getPropertyFromAspect')->willReturn(true);
        GeneralUtility::setSingletonInstance(Context::class, $context);

        $mockQueryBuilder = new DummyQueryBuilder($this);
        $mockQueryBuilder->result->method('fetchAssociative')->willReturn($record);

        $arguments = ['table' => 'table'];

        $this->subject->setArguments($arguments);

        self::assertSame($record, $this->subject->getRecord(1));
    }

    public function testGetResource(): void
    {
        self::assertSame('input', $this->subject->getResource('input'));
    }

    public function testGetResources(): void
    {
        $this->subject->setArguments(['field' => 'field']);
        self::assertSame(['a', 'b', 'c'], $this->subject->getResources(['field' => 'a,b,c']));
    }

    public function testThrowsExceptionWithoutTable(): void
    {
        self::expectExceptionCode(1384611336);
        $this->subject->getTable();
    }

    public function testThrowsExceptionWithoutField(): void
    {
        self::expectExceptionCode(1384611355);
        $this->subject->getField();
    }

    public function testRenderThrowsExceptionWithoutRecord(): void
    {
        $queryBuilder = new DummyQueryBuilder($this);
        $queryBuilder->result->method('fetchAssociative')->willReturn(false);

        self::expectExceptionCode(1384611413);
        $this->subject->setArguments(['uid' => 123, 'table' => 'table', 'field' => 'field']);
        $this->subject->render();
    }
}
