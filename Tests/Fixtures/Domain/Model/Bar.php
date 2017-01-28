<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Bar
 */
class Bar extends AbstractEntity
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo
     */
    protected $foo;

    /**
     * @var ObjectStorage<\FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Bar>
     */
    protected $bars = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bars = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Foo
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param Foo $foo
     * @return void
     */
    public function setFoo(Foo $foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return ObjectStorage<Bar>
     */
    public function getBars()
    {
        return $this->bars;
    }

    /**
     * @param ObjectStorage<Bar> $bars
     */
    public function setbars($bars)
    {
        $this->bars = $bars;
    }
}
