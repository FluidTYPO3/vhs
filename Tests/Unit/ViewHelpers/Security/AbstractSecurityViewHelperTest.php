<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Security;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class AbstractSecurityViewHelperTest
 */
class AbstractSecurityViewHelperTest extends AbstractViewHelperTest {

	/**
	 * @test
	 */
	public function canCreateViewHelperInstance() {
		$instance = $this->getMockForAbstractClass($this->getViewHelperClassName());
		$instance->injectReflectionService($this->objectManager->get('TYPO3\\CMS\\Extbase\\Reflection\\ReflectionService'));
		$this->assertInstanceOf($this->getViewHelperClassName(), $instance);
	}

	/**
	 * @test
	 */
	public function canPrepareArguments() {
		$instance = $this->getMockForAbstractClass(
			$this->getViewHelperClassName(), array(), '', FALSE, FALSE, FALSE, array('registerRenderMethodArguments')
		);
		$instance->expects($this->any())->method('registerRenderMethodArguments');
		$instance->injectReflectionService($this->objectManager->get('TYPO3\\CMS\\Extbase\\Reflection\\ReflectionService'));
		$this->assertNotEmpty($instance->prepareArguments());
	}

}
