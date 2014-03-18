<?php
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

/**
 * @author Björn Fromme <fromme@dreipunktnull.com>, dreipunktnull
 * @package Vhs
 */
class Tx_Vhs_Tests_Fixtures_Domain_Model_Foo extends Tx_Extbase_DomainObject_AbstractEntity {

    /**
     * @var string
     */
    protected $bar;

	/**
	 * @var Tx_Vhs_Tests_Fixtures_Domain_Model_Foo
	 */
	protected $foo;

    /**
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Vhs_Tests_Fixtures_Domain_Model_Foo>
     */
    protected $children;

    public function __construct() {
        $this->bar = 'baz';
        $this->children = new Tx_Extbase_Persistence_ObjectStorage();
    }

    /**
     * @return string
     */
    public function getBar() {
        return $this->bar;
    }

	/**
	 * @param \Tx_Vhs_Tests_Fixtures_Domain_Model_Foo $foo
	 */
	public function setFoo($foo) {
		$this->foo = $foo;
	}

	/**
	 * @return \Tx_Vhs_Tests_Fixtures_Domain_Model_Foo
	 */
	public function getFoo() {
		return $this->foo;
	}

    /**
     * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Vhs_Tests_Fixtures_Domain_Model_Foo>
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @param Tx_Vhs_Tests_Fixtures_Domain_Model_Foo $child
     * @return Tx_Vhs_Tests_Fixtures_Domain_Model_Foo
     */
    public function addChild(Tx_Vhs_Tests_Fixtures_Domain_Model_Foo $child) {
        $this->children->attach($child);

        return $this;
    }
}