<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Foo
 */
class LegacyFoo extends Foo
{
    /**
     * @var string
     * @validate NotEmpty
     */
    protected $bar;

    /**
     * @var LegacyFoo
     */
    protected $foo;

    /**
     * @var ObjectStorage<Foo>
     */
    protected $children;

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
     * @return string
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * @param LegacyFoo $foo
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return LegacyFoo
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @return ObjectStorage<Foo>
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Foo $child
     * @return Foo
     */
    public function addChild(Foo $child)
    {
        $this->children->attach($child);

        return $this;
    }
}
