<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Variable;

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
    /**
     * @test
     */
    public function returnsNullIfVariableDoesNotExist()
    {
        $this->assertNull($this->executeViewHelper(['name' => 'void', []]));
    }

    /**
     * @test
     */
    public function returnsDirectValueIfExists()
    {
        $this->assertEquals(1, $this->executeViewHelper(['name' => 'test'], ['test' => 1]));
    }

    /**
     * @test
     */
    public function returnsNestedValueIfRootExists()
    {
        $this->assertEquals(1, $this->executeViewHelper(['name' => 'test.test'], ['test' => ['test' => 1]]));
    }

    /**
     * @test
     */
    public function returnsNestedValueUsingRawKeysIfRootExists()
    {
        $this->assertEquals(1, $this->executeViewHelper(['name' => 'test.test', 'useRawKeys' => true], ['test' => ['test' => 1]]));
    }

    /**
     * @test
     */
    public function returnsNestedValueIfRootExistsAndMembersAreNumeric()
    {
        $this->assertEquals(2, $this->executeViewHelper(['name' => 'test.1'], ['test' => [1, 2]]));
    }

    /**
     * @test
     */
    public function returnsNullAndSuppressesExceptionOnInvalidPropertyGetting()
    {
        $user = new Foo();
        $this->assertEquals(null, $this->executeViewHelper(['name' => 'test.void'], ['test' => $user]));
    }

    /**
     * @test
     */
    public function returnsNullOnNonExistingObjectStorageProperty() {
        $objectStorage = new ObjectStorage();
        $this->assertNull($this->executeViewHelper(['name' => 'storage.15'], ['storage' => $objectStorage]));
    }
}
