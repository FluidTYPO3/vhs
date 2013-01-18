<?php

class Tx_Vhs_Tests_Fixtures_Domain_Model_Foo extends Tx_Extbase_DomainObject_AbstractEntity {

    /**
     * @var string
     */
    protected $bar;

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