<?php

namespace FluidTYPO3\Vhs\Tests\Functional\ViewHelpers;

class CountMathSystemViewHelperTest extends AbstractFunctionalViewHelperCase
{
    /**
     * @dataProvider countTemplates
     */
    public function testCountViewHelpers(string $source, string $expected, array $variables = []): void
    {
        $this->assertTemplateRenders($expected, $source, $variables);
    }

    public function countTemplates(): array
    {
        return [
            'bytes' => ['<v:count.bytes string="string with spaces" encoding="UTF-8" />', '18'],
            'lines empty' => ['<v:count.lines string="" />', '0'],
            'lines with breaks' => ["<v:count.lines string=\"word with \n one line break\" />", '2'],
            'substring' => ['<v:count.substring haystack="foobar baz bar" string="bar" />', '2'],
            'words' => ['<v:count.words string="string with four words" />', '4'],
            'words with html' => ['<v:count.words string="{content}" />', '4', ['content' => 'string <b>with HTML</b> inside']],
        ];
    }

    /**
     * @dataProvider mathTemplates
     */
    public function testMathViewHelpers(string $source, string $expected, array $variables = []): void
    {
        $this->assertTemplateRenders($expected, $source, $variables);
    }

    public function mathTemplates(): array
    {
        return [
            'average scalar' => ['<v:math.average a="1" b="3" fail="0" />', '2'],
            'ceil' => ['<v:math.ceil a="0.5" fail="0" />', '1'],
            'cube' => ['<v:math.cube a="2" fail="0" />', '8'],
            'cubic root' => ['<v:math.cubicRoot a="8" fail="0" />', '2'],
            'division' => ['<v:math.division a="4" b="2" fail="0" />', '2'],
            'floor' => ['<v:math.floor a="1.5" fail="0" />', '1'],
            'maximum scalar' => ['<v:math.maximum a="4" b="2" fail="0" />', '4'],
            'maximum array' => ['<v:math.maximum a="{numbers}" fail="0" />', '3', ['numbers' => [1, 3]]],
            'median odd' => ['<v:math.median a="{numbers}" fail="0" />', '2', ['numbers' => [1, 2, 3]]],
            'median even' => ['<v:math.median a="{numbers}" fail="0" />', '2.5', ['numbers' => [1, 2, 3, 4]]],
            'minimum scalar' => ['<v:math.minimum a="4" b="2" fail="0" />', '2'],
            'modulo' => ['<v:math.modulo a="3" b="2" fail="0" />', '1'],
            'power' => ['<v:math.power a="8" b="2" fail="0" />', '64'],
            'product scalar' => ['<v:math.product a="8" b="2" fail="0" />', '16'],
            'range' => [
                '<f:for each="{v:math.range(a: numbers, fail: 0)}" as="number">{number}</f:for>',
                '28',
                ['numbers' => [2, 4, 6, 3, 8]],
            ],
            'round' => ['<v:math.round a="0.5" fail="0" />', '1'],
            'square root' => ['<v:math.squareRoot a="9" fail="0" />', '3'],
            'square' => ['<v:math.square a="3" fail="0" />', '9'],
            'subtract' => ['<v:math.subtract a="8" b="2" fail="0" />', '6'],
            'sum' => ['<v:math.sum a="8" b="2" fail="0" />', '10'],
        ];
    }

    public function testRandomStringHonorsLengthAndCharacterList(): void
    {
        $result = $this->executeTemplate(
            '<v:random.string minimumLength="32" maximumLength="32" characters="abcdef" />'
        );
        self::assertMatchesRegularExpression('/^[a-f]{32}$/', (string) $result);
    }

    public function testRandomNumberHonorsIntegerMode(): void
    {
        $result = $this->executeTemplate(
            '<v:random.number minimum="100" maximum="999" minimumDecimals="0" maximumDecimals="0" />'
        );
        self::assertMatchesRegularExpression('/^[0-9]{3}$/', (string) $result);
    }

    public function testSystemTimestampRendersCurrentOrFutureTimestamp(): void
    {
        $now = time();
        $result = (int) $this->executeTemplate('<v:system.timestamp />');
        self::assertGreaterThanOrEqual($now, $result);
    }

    public function testSystemUniqIdRendersDifferentValues(): void
    {
        $first = $this->executeTemplate('<v:system.uniqId />');
        $second = $this->executeTemplate('<v:system.uniqId />');
        self::assertNotSame($first, $second);
    }

    public function testSystemDateTimeCanBeFormattedByFluid(): void
    {
        $result = $this->executeTemplate('<v:format.json.encode value="{v:system.dateTime()}" />');
        self::assertMatchesRegularExpression('/^[0-9]+$/', (string) $result);
    }
}
