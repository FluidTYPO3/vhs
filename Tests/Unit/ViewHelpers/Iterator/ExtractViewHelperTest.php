<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Iterator;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTestCase;
use FluidTYPO3\Vhs\ViewHelpers\Iterator\ExtractViewHelper;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class ExtractViewHelperTest
 */
class ExtractViewHelperTest extends AbstractViewHelperTestCase
{
    /**
     * @var ExtractViewHelper
     */
    protected $fixture;

    /**
     * @return ObjectStorage<object>
     */
    private static function constructObjectStorageContainingFrontendUser(): ObjectStorage
    {
        $storage = new ObjectStorage();
        $user1 = self::createObjectWithFirstName('Peter');
        $user2 = self::createObjectWithFirstName('Paul');
        $user3 = self::createObjectWithFirstName('Mary');
        $storage->attach($user1);
        $storage->attach($user2);
        $storage->attach($user3);

        return $storage;
    }

    private static function createObjectWithFirstName(string $firstName): object
    {
        return new class ($firstName) {
            public function __construct(private readonly string $firstName)
            {
            }

            public function getFirstName(): string
            {
                return $this->firstName;
            }
        };
    }

    /**
     * @test
     * @dataProvider nestedStructures
     */
    public function recursivelyExtractKey(mixed $structure, string $key, mixed $expected): void
    {
        $recursive = true;
        $this->assertEquals(
            $expected,
            $this->executeViewHelper(['content' => $structure, 'key' => $key, 'recursive' => true, 'single' => false])
        );
    }

    /**
     * @return array
     */
    public static function nestedStructures(): array
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
                self::constructObjectStorageContainingFrontendUser(),
                'firstname',
                [
                    'Peter',
                    'Paul',
                    'Mary'
                ]
            ],
            'empty array' => [
                [],
                'qux',
                [],
            ],
        ];

        return $structures;
    }

    /**
     * @test
     * @dataProvider simpleStructures
     */
    public function extractByKeyExtractsKeyByPath(mixed $structure, string $key, mixed $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->executeViewHelper(['content' => $structure, 'key' => $key, 'recursive' => false, 'single' => false])
        );
    }

    /**
     * @test
     * @dataProvider simpleStructures
     */
    public function extractByKeyExtractsKeyByPathWithSingle(mixed $structure, string $key, mixed $expected): void
    {
        if (is_array($expected)) {
            $expected = reset($expected);
        }
        $this->assertEquals(
            $expected,
            $this->executeViewHelper(['content' => $structure, 'key' => $key, 'recursive' => false, 'single' => true])
        );
    }

    /**
     * @return array
     */
    public static function simpleStructures(): array
    {
        $structures = [
            // structure, key, expected
            'flat associative array' => [
                ['myKey' => 'myValue'],
                'myKey',
                'myValue'
            ],
            'flat associative array with array as value' => [
                ['myKey' => ['foo' => 'myValue']],
                'myKey',
                ['foo' => 'myValue'],
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
            'empty array' => [
                [],
                'qux',
                null,
            ],
        ];

        return $structures;
    }
}
