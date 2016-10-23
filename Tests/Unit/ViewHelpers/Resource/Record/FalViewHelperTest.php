<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Resource\Record;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;

/**
 * Class FalViewHelperTest
 */
class FalViewHelperTest extends AbstractViewHelperTest
{
    /**
     * @test
     */
    public function testFalViewhHelperWorkspaceHandling()
    {
        $GLOBALS['TYPO3_DB'] = $this->getMockBuilder(DatabaseConnection::class)->getMock();
        $GLOBALS['TYPO3_DB']->expects($this->once())
            ->method('exec_SELECTgetRows')
            ->with(
                'uid',
                'sys_file_reference',
                'tablenames=' .
                ' AND uid_foreign=0' .
                ' AND fieldname='
                . 'AND sys_file_reference.deleted=0 AND (sys_file_reference.t3ver_wsid=0 OR sys_file_reference.t3ver_wsid=1234) AND sys_file_reference.pid<>-1',
                '',
                'sorting_foreign',
                '',
                'uid'
            )
            ->will($this->returnValue(['foo']));
        $viewHelper = $this->createInstance();
        $viewHelperNode = $this->createViewHelperNode($viewHelper, []);
        $GLOBALS['BE_USER']->workspaceRec['uid'] = 1234;
        $result = $this->executeViewHelper(['table' => 'pages', 'field' => 'media'], [], $viewHelperNode);
    }

    /**
     * @test
     */
    public function testFalViewhHelperWithoutWorkspaces()
    {
        $GLOBALS['TYPO3_DB'] = $this->getMockBuilder(DatabaseConnection::class)->getMock();
        $GLOBALS['TYPO3_DB']->expects($this->once())
            ->method('exec_SELECTgetRows')
            ->with(
                'uid',
                'sys_file_reference',
                'tablenames=' .
                ' AND uid_foreign=0' .
                ' AND fieldname='
                . 'AND sys_file_reference.deleted=0 AND sys_file_reference.t3ver_state<=0 AND sys_file_reference.pid<>-1 AND sys_file_reference.hidden=0',
                '',
                'sorting_foreign',
                '',
                'uid'
            )
            ->will($this->returnValue(['foo']));
        $viewHelper = $this->createInstance();
        $viewHelperNode = $this->createViewHelperNode($viewHelper, []);
        $result = $this->executeViewHelper(['table' => 'pages', 'field' => 'media'], [], $viewHelperNode);
    }
}
