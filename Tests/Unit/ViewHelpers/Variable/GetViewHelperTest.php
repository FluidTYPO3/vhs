<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;
use PHPUnit\Framework\Attributes\Test;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class GetViewHelperTest
 */
class GetViewHelperTest extends AbstractViewHelperTestCase
{
    #[Test]
    public function returnsNullIfVariableDoesNotExist(): void
    {
        $this->assertNull($this->executeViewHelper(['name' => 'void', []]));
    }

    #[Test]
    public function returnsDirectValueIfExists(): void
    {
        $this->assertEquals(1, $this->executeViewHelper(['name' => 'test'], ['test' => 1]));
    }

    #[Test]
    public function returnsNestedValueIfRootExists(): void
    {
        $this->assertEquals(1, $this->executeViewHelper(['name' => 'test.test'], ['test' => ['test' => 1]]));
    }

    #[Test]
    public function returnsNestedValueUsingRawKeysIfRootExists(): void
    {
        $this->assertEquals(
            1,
            $this->executeViewHelper(['name' => 'test.test', 'useRawKeys' => true], ['test' => ['test' => 1]])
        );
    }

    #[Test]
    public function returnsNestedValueIfRootExistsAndMembersAreNumeric(): void
    {
        $this->assertEquals(2, $this->executeViewHelper(['name' => 'test.1'], ['test' => [1, 2]]));
    }

    #[Test]
    public function returnsNullAndSuppressesExceptionOnInvalidPropertyGetting(): void
    {
        $user = new Foo();
        $this->assertEquals(null, $this->executeViewHelper(['name' => 'test.void'], ['test' => $user]));
    }

    #[Test]
    public function returnsNullOnNonExistingObjectStorageProperty(): void
    {
        $objectStorage = new ObjectStorage();
        $this->assertNull($this->executeViewHelper(['name' => 'storage.15'], ['storage' => $objectStorage]));
    }
}
