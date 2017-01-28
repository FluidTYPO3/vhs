<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Form;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Foo;
use FluidTYPO3\Vhs\Tests\Fixtures\Domain\Model\Bar;
use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class SelectViewHelperTest
 */
class SelectViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @dataProvider getRenderTestValues
     * @param array $arguments
     * @param string $expected
     */
    public function testRender(array $arguments, $expected)
    {
        $result = $this->executeViewHelper($arguments);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getRenderTestValues()
    {
        $model1 = new Foo();
        $model1->setName('Model1');
        $model2 = new Bar();
        $model2->setName('Model2');
        $model1id = spl_object_hash($model1);
        $model2id = spl_object_hash($model2);
        $model1name = Foo::class . ':';
        $model2name = Bar::class . ':';
        return [
            [[], '<select name="" />'],
            [['name' => 'test'], '<select name="test" />'],
            [
                ['name' => 'test', 'multiple' => true],
                '<input type="hidden" name="test" value="" /><select name="test[]" multiple="multiple" />'
            ],
            [
                ['name' => 'test', 'multiple' => true, 'selectAllbyDefault' => true, 'value' => 'test'],
                '<input type="hidden" name="test" value="" /><select name="test[]" multiple="multiple" />'
            ],
            [
                [
                    'name' => 'test', 'multiple' => true, 'selectAllbyDefault' => true, 'value' => [$model1id, $model1id],
                    'optionLabelField' => 'name'
                ],
                '<input type="hidden" name="test" value="" /><select name="test[]" multiple="multiple" />'
            ],
            [
                ['name' => 'foobar', 'options' => ['foo' => 'bar']],
                '<select name="foobar"><option value="foo">bar</option>' . PHP_EOL . '</select>'
            ],
            [
                ['name' => 'foobar', 'options' => ['foo' => 'bar'], 'value' => 'foo'],
                '<select name="foobar"><option value="foo" selected="selected">bar</option>' . PHP_EOL . '</select>'
            ],
            [
                ['name' => 'foobar', 'options' => [$model1]],
                '<select name="foobar"><option value="' . $model1id . '">' . $model1name . '</option>' . PHP_EOL . '</select>'
            ],
            [
                ['name' => 'foobar', 'options' => [$model1], 'value' => $model1],
                '<select name="foobar[__identity]"><option value="' . $model1id . '" selected="selected">'
                . $model1name .'</option>' . PHP_EOL . '</select>'
            ],
            [
                ['name' => 'foobar', 'options' => [$model1, $model2]],
                '<select name="foobar"><option value="' . $model1id . '">' . $model1name . '</option>' . PHP_EOL
                . '<option value="' . $model2id . '">' . $model2name . '</option>' . PHP_EOL . '</select>'
            ],
            [
                ['name' => 'foobar', 'options' => [$model1], 'optionLabelField' => 'name'],
                '<select name="foobar"><option value="' . $model1id . '">' . $model1->getName() . '</option>' . PHP_EOL . '</select>'
            ],
            [
                ['name' => 'foobar', 'options' => [$model1], 'optionLabelField' => 'bar'],
                '<select name="foobar"><option value="' . $model1id . '">baz</option>' . PHP_EOL . '</select>'
            ],
            [
                ['name' => 'foobar', 'options' => [$model1], 'optionValueField' => 'bar'],
                '<select name="foobar"><option value="baz">' . $model1name . '</option>' . PHP_EOL . '</select>'
            ],
        ];
    }
}
