<?php
/**
 * Created by PhpStorm.
 * User: jeff
 * Date: 07/08/2015
 * Time: 15:21
 */

namespace Goblinoid\Util\Traversable;

class EnsureTraversableTest extends \PHPUnit_Framework_TestCase
{
    /**
         * @dataProvider getTestCases
         * @param mixed $input
         * @param array|\Traversable $expected
         */
        public function testBasicUsage($input, $expected)
        {
            $ensure_traversable = new EnsureTraversable();

            $this->assertEquals($expected, $ensure_traversable->apply($input));
        }

        /**
         * @dataProvider getTestCases
         * @param mixed $input
         * @param array|\Traversable $expected
         */
        public function testInvoke($input, $expected)
        {
            $ensure_traversable = new EnsureTraversable();

            $this->assertEquals($expected, $ensure_traversable($input));
        }

    public function getTestCases()
    {
        $stdClass = new \stdClass();
        $traversable = new \ArrayIterator([]);

        return [
                ['string', ['string']],
                [1, [1]],
                [2.3, [2.3]],
                [null, []],
                [[],[]],
                [[1], [1]],
                [[1, 2], [1, 2]],
                [$stdClass, [$stdClass]],
                [[$stdClass, $stdClass], [$stdClass, $stdClass]],
                [[[1,2],[3]], [[1,2],[3]]],
                [$traversable, $traversable],
                [[$traversable, $stdClass], [$traversable, $stdClass]],
            ];
    }
}
