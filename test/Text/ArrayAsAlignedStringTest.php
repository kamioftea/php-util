<?php
    /**
     * Created by PhpStorm.
     * User: jeff
     * Date: 09/09/2015
     * Time: 12:44
     */
    
    namespace Goblinoid\Util\Text;
    
    
    class ArrayAsAlignedStringTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @param $array
         * @param $expected
         * @dataProvider getTestCases
         */
        public function testFiltersTestCases($array, $expected)
        {
            $filter = new ArrayAsAlignedString();
            $this->assertEquals($expected, $filter->apply($array));
        }
        
        /**
         * @param $array
         * @param $expected
         * @dataProvider getTestCases
         */
        public function testInvokeTestCases($array, $expected)
        {
            $filter = new ArrayAsAlignedString();
            $this->assertEquals($expected, $filter($array));
        }

        public function getTestCases()
        {
            return [
                'Basic Usage' => [
                    'array' => ['Hello' => 'World'],
                    'expected' => 'Hello: World',
                ],
                'Indents Correctly' => [
                    'array' => ['Hello' => 'World', 'Much Longer Key' => 'Has a Value'],
                    'expected' => 'Hello:           World' . PHP_EOL .
                                  'Much Longer Key: Has a Value',
                ],
                'MB Safe' => [
                    'array' => ['Hello' => 'World', '€' => 'Euro', '€€€€€€€' => 'Euros'],
                    'expected' => 'Hello:   World' . PHP_EOL .
                                  '€:       Euro' . PHP_EOL .
                                  '€€€€€€€: Euros',
                ],
                'Indents subsequent lines - basic' => [
                    'array' => ['Hello' => 'World' . PHP_EOL . 'Universe'],
                    'expected' => 'Hello: World' . PHP_EOL .
                                  '       Universe'
                ],
                'Indents subsequent lines - with others' => [
                    'array' => [ 'k' => 'v', 'Hello' => 'World' . PHP_EOL . 'Universe', 'Much Longer Key' => 'Has a Value'],
                    'expected' => 'k:               v' . PHP_EOL .
                                  'Hello:           World' . PHP_EOL .
                                  '                 Universe' . PHP_EOL .
                                  'Much Longer Key: Has a Value',
                ],
            ];
        }
    }
