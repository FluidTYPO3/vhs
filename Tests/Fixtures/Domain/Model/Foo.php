<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 */
class Foo extends AbstractEntity {

    /**
     * @var string
     */
    protected $bar;

	/**
	 * @var Foo
	 */
	protected $foo;

    /**
     * @var ObjectStorage<Foo>
     */
    protected $children;

    public function __construct() {
        $this->bar = 'baz';
        $this->children = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getBar() {
        return $this->bar;
    }

	/**
	 * @param Foo $foo
	 */
	public function setFoo($foo) {
		$this->foo = $foo;
	}

	/**
	 * @return Foo
	 */
	public function getFoo() {
		return $this->foo;
	}

    /**
     * @return ObjectStorage<Foo>
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @param Foo $child
     * @return Foo
     */
    public function addChild(Foo $child) {
        $this->children->attach($child);

        return $this;
    }
}