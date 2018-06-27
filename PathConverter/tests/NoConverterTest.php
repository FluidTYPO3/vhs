<?php

namespace MatthiasMullie\PathConverter\Tests;

use MatthiasMullie\PathConverter\NoConverter;

/**
 * Converter test case.
 */
class NoConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Converter, provided by dataProvider.
     *
     * @test
     * @dataProvider dataProvider
     */
    public function convert($relative, $expected)
    {
        $converter = new NoConverter();
        $result = $converter->convert($relative);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array [relative, from, to, expected result]
     */
    public function dataProvider()
    {
        $tests = array();

        $tests[] = array(
            '../images/img.jpg',
            '../images/img.jpg',
        );

        $tests[] = array(
            '../../images/icon.gif',
            '../../images/icon.gif',
        );

        // absolute path - doesn't make sense :)
        $tests[] = array(
            '/home/username/file.txt',
            '/home/username/file.txt',
        );

        $tests[] = array(
            'image.jpg',
            'image.jpg',
        );

        $tests[] = array(
            '../images/img.jpg',
            '../images/img.jpg',
        );

        // https://github.com/forkcms/forkcms/issues/1186
        $tests[] = array(
            '../images/img.jpg',
            '../images/img.jpg',
        );

        // https://github.com/matthiasmullie/path-converter/issues/1
        $tests[] = array(
            'image.jpg',
            'image.jpg',
        );

        return $tests;
    }
}
