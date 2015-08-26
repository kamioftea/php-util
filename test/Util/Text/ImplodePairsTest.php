<?php
/**
 * Created by PhpStorm.
 * User: jeff
 * Date: 06/08/2015
 * Time: 12:46
 */

namespace HTC\PhinxExtensions\Util\Text;

class ImplodePairsTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicUsage()
    {
        $implode_pairs = new ImplodePairs();
        $this->assertEquals('hello="world", test="data"', $implode_pairs->apply([
                'hello' => 'world',
                'test' => 'data',
            ]));
    }

    public function testPairGlue()
    {
        $implode_pairs = (new ImplodePairs())
                ->setPairGlue('|');
        $this->assertEquals('hello="world"|test="data"', $implode_pairs->apply([
                'hello' => 'world',
                'test' => 'data',
            ]));
    }

    public function testFormat()
    {
        $implode_pairs = (new ImplodePairs())
                ->setFormat('[$key $value]');
        $this->assertEquals('[hello world], [test data]', $implode_pairs->apply([
                'hello' => 'world',
                'test' => 'data',
            ]));
    }

    public function testCanBeInvokedWithArgs()
    {
        $implode_pairs = new ImplodePairs();
        $this->assertEquals('[hello world]|[test data]', $implode_pairs([
                'hello' => 'world',
                'test' => 'data',
            ], '|', '[$key $value]'));
    }

    public function testFormatCanBeEscaped()
    {
        $implode_pairs = (new ImplodePairs())
                ->setFormat('$$key$$$$value$$$key$t');

        $this->assertEquals('$key$$value$hello$t, $key$$value$test$t', $implode_pairs->apply([
                'hello' => 'world',
                'test' => 'data',
            ]));
    }

        /**
         * @dataProvider getEdgeCases
         * @param string[] $array
         * @param string $pair_glue
         * @param string $format
         * @param string $expected
         */
        public function testEdgeCases($array, $pair_glue, $format, $expected)
        {
            $implode_pairs = new ImplodePairs();
            $this->assertEquals($expected, $implode_pairs->apply($array, $pair_glue, $format));
        }

    public function getEdgeCases()
    {
        return [
                'possible placeholder/data conflict' => [
                    ['key' => 'value', 'value' => 'key'],
                    '$key',
                    'key=$key,value=$value',
                    'key=key,value=value$keykey=value,value=key'
                ],
                'empty array' => [
                    [],
                    null,
                    null,
                    ''
                ],
                'symbols' => [
                    ['$' => '%', '#' => '\'', '\\' => '/'],
                    '',
                    '$value$$$key',
                    '%$$\'$#/$\\'
                ],
            ];
    }
}
