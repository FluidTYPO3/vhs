<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\ViewHelpers\Iterator\ExtractViewHelper;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class ExtractViewHelperTest
 */
class ExtractViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @var ExtractViewHelper
     */
    protected $fixture;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->fixture = $this->getMockBuilder(ExtractViewHelper::class)->setMethods(['hasArgument'])->getMock();
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @return array
     */
    public function simpleStructures()
    {
        $structures = [
            // structure, key, expected
            'flat associative array' => [
                ['myKey' => 'myValue'],
                'myKey',
                'myValue'
            ],
            'deeper associative array' => [
                [
                    'myFirstKey' => [
                        'mySecondKey' => [
                            'myThirdKey' => 'myValue'
                        ]
                    ]
                ],
                'myFirstKey.mySecondKey.myThirdKey',
                'myValue'
            ],
        ];

        return $structures;
    }

    /**
     * @return ObjectStorage
     */
    public function constructObjectStorageContainingFrontendUser()
    {
        $storage = new ObjectStorage();
        $user1 = new FrontendUser();
        $user2 = new FrontendUser();
        $user3 = new FrontendUser();
        $user1->setFirstName('Peter');
        $user2->setFirstName('Paul');
        $user3->setFirstName('Marry');
        $storage->attach($user1);
        $storage->attach($user2);
        $storage->attach($user3);

        return $storage;
    }

    /**
     * @return ObjectStorage
     */
    public function constructObjectStorageContainingFrontendUsersWithUserGroups()
    {
        $storage = new ObjectStorage();
        $userGroup1 = new FrontendUserGroup('my first group');
        $userGroup2 = new FrontendUserGroup('my second group');
        $user1 = new FrontendUser();
        $user2 = new FrontendUser();
        $user1->addUsergroup($userGroup1);
        $user2->addUsergroup($userGroup2);
        $storage->attach($user1);
        $storage->attach($user2);

        return $storage;
    }

    /**
     * @return array
     */
    public function nestedStructures()
    {
        $structures = [
            // structure, key, expected
            'simple indexed_search searchWords array' => [
                [
                    0 => [
                        'sword' => 'firstWord',
                        'oper' => 'AND'
                    ],
                ],
                'sword',
                [
                    'firstWord'
                ]
            ],
            'interesting indexed_search searchWords array' => [
                [
                    0 => [
                        'sword' => 'firstWord',
                        'oper' => 'AND'
                    ],
                    1 => [
                        'sword' => 'secondWord',
                        'oper' => 'AND'
                    ],
                    3 => [
                        'sword' => 'thirdWord',
                        'oper' => 'AND'
                    ]
                ],
                'sword',
                [
                    'firstWord',
                    'secondWord',
                    'thirdWord'
                ]
            ],
            'ridiculously nested array' => [
                [
                    [
                        [
                            [
                                [
                                    [
                                        'l' => 'some'
                                    ]
                                ]
                            ],
                            [
                                'l' => 'text'
                            ]
                        ]
                    ]
                ],
                'l',
                [
                    0 => 'some',
                    1 => 'text',
                ]
            ],
            'ObjectStorage containing FrontendUser' => [
                $this->constructObjectStorageContainingFrontendUser(),
                'firstname',
                [
                    'Peter',
                    'Paul',
                    'Marry'
                ]
            ],
        ];

        return $structures;
    }

    /**
     * @test
     * @dataProvider nestedStructures
     */
    public function recursivelyExtractKey($structure, $key, $expected)
    {
        $recursive = true;
        $this->assertSame(
            $expected,
            $this->fixture->render($key, $structure, $recursive)
        );
    }

    /**
     * @test
     * @dataProvider simpleStructures
     */
    public function extractByKeyExtractsKeyByPath($structure, $key, $expected)
    {
        $this->assertSame(
            $expected,
            $this->fixture->extractByKey($structure, $key)
        );
    }
}
