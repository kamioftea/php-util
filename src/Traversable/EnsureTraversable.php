<?php
/**
 * Created by PhpStorm.
 * User: jeff
 * Date: 07/08/2015
 * Time: 15:18
 */

namespace HTC\PhinxExtensions\Util\Traversable;

class EnsureTraversable
{
    /**
     * @param mixed $var
     * @return array|\Traversable
     */
    public function __invoke($var)
    {
        return $this->apply($var);
    }

    /**
     * Get around php (array)$object => array of object's properties
     *
     * @param mixed $var
     * @return array|\Traversable
     */
    public function apply($var)
    {
        switch (true) {
            case $var instanceof \Traversable:
                return $var;
            case is_object($var):
                return [$var];
            default:
                // [null] => array(0=>null) but (array) null  = [];
                return (array) $var;
        }
    }
}
