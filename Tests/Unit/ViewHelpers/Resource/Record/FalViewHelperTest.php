<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Doctrine\DBAL\Driver\Result;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Resource\Record\FalViewHelper;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FalViewHelperTest
 */
class FalViewHelperTest extends AbstractViewHelperTestCase
{
    protected function setUp(): void
    {
        $this->singletonInstances[ResourceFactory::class] = $this->getMockBuilder(ResourceFactory::class)
            ->setMethods(['getFileReferenceObject'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->singletonInstances[FileRepository::class] = $this->getMockBuilder(FileRepository::class)
            ->setMethods(['findByRelation'])
            ->disableOriginalConstructor()
            ->getMock();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($GLOBALS['TSFE']);
    }

    public function testFalViewhHelperWithoutWorkspaces(): void
    {
        $output = $this->executeViewHelper(['table' => 'pages', 'field' => 'media']);
        $this->assertSame([], $output);
    }

    public function testGetResource(): void
    {
        $storage = $this->getMockBuilder(ResourceStorage::class)
            ->setMethods(['getFileInfo'])
            ->disableOriginalConstructor()
            ->getMock();
        $storage->method('getFileInfo')->willReturn(['foo' => 'bar']);

        $file = $this->getMockBuilder(File::class)
            ->setMethods(['getProperties', 'getStorage', 'toArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $file->method('getStorage')->willReturn($storage);
        $file->method('getProperties')->willReturn(['baz' => 'value']);
        $file->method('toArray')->willReturn([]);

        $fileReference = $this->getMockBuilder(FileReference::class)
            ->setMethods(['getOriginalFile', 'getProperties'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileReference->method('getOriginalFile')->willReturn($file);
        $fileReference->method('getProperties')->willReturn(['x' => 'y']);

        $subject = new FalViewHelper();
        $output = $subject->getResource($fileReference);

        self::assertSame(['baz' => 'value', 'foo' => 'bar', 'x' => 'y'], $output);
    }

    public function testGetResourcesWhenPageContext(): void
    {
        $this->singletonInstances[FileRepository::class]->method('findByRelation')->willReturn([]);

        $GLOBALS['TSFE'] = (object) ['sys_page' => 'foobar'];

        $arguments = ['table' => 'pages', 'field' => 'void'];
        $record = ['uid' => 1];
        $subject = new FalViewHelper();
        $subject->setArguments($arguments);
        $output = $subject->getResources($record);
        self::assertSame([], $output);
    }

    /**
     * @dataProvider getGetResourcesInNonPageContextTestValues
     */
    public function testGetResourcesInNonPageContext(int $workspaceUid): void
    {
        $file = $this->getMockBuilder(FileReference::class)->disableOriginalConstructor()->getMock();

        $this->singletonInstances[ResourceFactory::class]->method('getFileReferenceObject')->willReturn($file);

        $statement = $this->getMockBuilder(Result::class)->getMockForAbstractClass();
        $statement->method('fetchAllAssociative')->willReturn([['uid' => 1]]);

        $expressionBuilder = $this->getMockBuilder(ExpressionBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->setMethods(['expr', 'createNamedParameter', 'execute', 'select', 'from', 'where', 'andWhere', 'orderBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $queryBuilder->method('expr')->willReturn($expressionBuilder);
        $queryBuilder->method('createNamedParameter')->willReturn('named');
        $queryBuilder->method('execute')->willReturn($statement);
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('from')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('orderBy')->willReturnSelf();

        $connectionPool = $this->getMockBuilder(ConnectionPool::class)
            ->setMethods(['getQueryBuilderForTable'])
            ->disableOriginalConstructor()
            ->getMock();
        $connectionPool->method('getQueryBuilderForTable')->willReturn($queryBuilder);

        GeneralUtility::addInstance(ConnectionPool::class, $connectionPool);

        $GLOBALS['BE_USER'] = (object) ['workspaceRec' => ['uid' => $workspaceUid]];

        $arguments = ['table' => 'tt_content', 'field' => 'void', 'asObjects' => true];
        $record = ['uid' => 1];
        $subject = new FalViewHelper();
        $subject->setArguments($arguments);
        $output = $subject->getResources($record);
        self::assertSame([$file], $output);
    }

    public function getGetResourcesInNonPageContextTestValues(): array
    {
        return [
            'without active workspace' => [0],
            'with active workspace' => [1],
        ];
    }
}
