<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Class OrViewHelperTest
 */
class OrViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param mixed $expected
     */
    public function testRender($arguments, $expected)
    {
        $result = $this->executeViewHelper($arguments);
        $content = $arguments['content'];
        unset($arguments['content']);
        $result2 = $this->executeViewHelperUsingTagContent((string) $content, $arguments);
        $this->assertEquals($expected, $result);
        $this->assertEquals($result, $result2);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        return [
            [['extensionName' => 'Vhs', 'content' => 'alt', 'alternative' => 'alternative'], 'alt'],
            [['extensionName' => 'Vhs', 'content' => '', 'alternative' => 'alternative'], 'alternative'],
            [['extensionName' => 'Vhs', 'content' => null, 'alternative' => 'alternative'], 'alternative'],
            [['extensionName' => 'Vhs', 'content' => 0, 'alternative' => 'alternative'], 'alternative'],
            /*
			array(
				array(
					'extensionName' => 'Vhs',
					'content' => 0,
					'alternative' => 'LLL:EXT:extensionmanager/Resources/Private/Language/locallang.xlf:extensionManager'
				),
				'Extension Manager'
			),
			array(
				array(
					'extensionName' => 'Vhs',
					'content' => 0,
					'alternative' => 'LLL:extensionManager',
					'extensionName' => 'extensionmanager'
				),
				'Extension Manager'
			),
			*/
            [
                ['extensionName' => 'Vhs', 'content' => 0, 'alternative' => 'LLL:notfound'],
                'LLL:notfound'
            ],
        ];
    }
}
