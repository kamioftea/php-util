<?php
/**
 * Created by PhpStorm.
 * User: jeff
 * Date: 06/08/2015
 * Time: 13:25
 */

namespace Goblinoid\Util\Text;

class CamelCaseToSeparatorTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicUsage()
    {
        $camel_case_to_separator = new CamelCaseToSeparator();
        $this->assertEquals('Hello World', $camel_case_to_separator->apply('HelloWorld'));
    }

    public function testConstructWIthSeparator()
    {
        $camel_case_to_separator = (new CamelCaseToSeparator('-'));
        $this->assertEquals('Hello-World', $camel_case_to_separator->apply('HelloWorld'));
    }

    public function testCanChangeSeparator()
    {
        $camel_case_to_separator = (new CamelCaseToSeparator())
                ->setSeparator('-');
        $this->assertEquals('Hello-World', $camel_case_to_separator->apply('HelloWorld'));
    }

    public function testCanInvokeWithArgs()
    {
        $camel_case_to_separator = new CamelCaseToSeparator();
        $this->assertEquals('Hello-World', $camel_case_to_separator('HelloWorld', '-'));
        $this->assertEquals(' ', $camel_case_to_separator->getSeparator());
    }

        /**
         * @dataProvider getEdgeCases
         *
         * @param string $string
         * @param string $separator
         * @param string $expected
         * @return array
         */
        public function testEdgeCases($string, $separator, $expected)
        {
            $camel_case_to_separator = new CamelCaseToSeparator();
            $this->assertEquals($expected, $camel_case_to_separator->apply($string, $separator));
        }

    public function getEdgeCases()
    {
        return [
                'all the things' => ['CSSBuyerAddress13HTMLTest', ' ', 'CSS Buyer Address 13 HTML Test'],
                'not CamelCased' => ['not_camel_cased', '_', 'not_camel_cased'],
                'all caps' => ['ALLCAPS', null, 'ALLCAPS'],
                'start lower cased' => ['startLowerCase', null, 'start Lower Case'],
            ];
    }
}
