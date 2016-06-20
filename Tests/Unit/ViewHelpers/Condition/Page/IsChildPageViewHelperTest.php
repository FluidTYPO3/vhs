<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Condition\Page;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class IsChildPageViewHelperTest
 */
class IsChildPageViewHelperTest extends AbstractViewHelperTest
{

    public function testRender()
    {
        $GLOBALS['TYPO3_DB'] = $this->getMock('TYPO3\\CMS\\Core\\Database\\DatabaseConnection', array('exec_SELECTquery'), array(), '', false);
        $GLOBALS['TYPO3_DB']->expects($this->any())->method('exec_SELECTquery')->will($this->returnValue(false));
        $arguments = array(
            'then' => 'then',
            'else' => 'else',
            'pageUid' => 0
        );
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals('else', $result);

        $staticResult = $this->executeViewHelperStatic($arguments);
        $this->assertEquals($result, $staticResult, 'The regular viewHelper output doesn\'t match the static output!');
    }
}
