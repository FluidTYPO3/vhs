<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbBackend;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * Class DummyPersistenceBackend
 */
class DummyPersistenceBackend extends Typo3DbBackend {

	/**
	 * @param PersistenceManagerInterface $manager
	 * @return void
	 */
	public function setPersistenceManager(PersistenceManagerInterface $manager) {

	}

}
