<?php
/**
 * Created by PhpStorm.
 * User: jeff
 * Date: 06/08/2015
 * Time: 12:31
 */

namespace HTC\PhinxExtensions\Util\Text;

class ImplodePairs
{
    private $pair_glue = ', ';
    private $format = '$key="$value"';

        /**
         * alias for filter
         *
         * @param array $array
         * @param null  $pair_glue
         * @param null  $format
         * @return string
         */
        public function __invoke(array $array, $pair_glue = null, $format = null)
        {
            return $this->apply($array, $pair_glue, $format);
        }

        /**
         * @param array       $array
         * @param string|null $pair_glue
         * @param string|null $format
         * @return string
         */
        public function apply(array $array, $pair_glue = null, $format = null)
        {
            $pair_glue = $pair_glue !== null ? $pair_glue : $this->getPairGlue();
            $format = $format !== null ? $format : $this->getFormat();

            array_walk($array, function (&$value, $key) use ($format) {
                $value = preg_replace_callback(
                    '/(\\$+)(key|value)/',
                    function ($matches) use ($key, $value) {
                        list(, $dollars, $var) = $matches;

                        $prefix = substr($dollars, 0, floor(strlen($dollars) / 2));

                        if (strlen($dollars) % 2 === 0) {
                            return $prefix . $var;
                        }

                        return $prefix . $$var;
                    }, $format);
            });

            return implode($pair_glue, $array);
        }

        /**
         * @return string
         */
        public function getFormat()
        {
            return $this->format;
        }

        /**
         * @param string $format
         * @return $this
         */
        public function setFormat($format)
        {
            $this->format = $format;

            return $this;
        }

        /**
         * @return string
         */
        public function getPairGlue()
        {
            return $this->pair_glue;
        }

        /**
         * @param string $pair_glue
         * @return $this
         */
        public function setPairGlue($pair_glue)
        {
            $this->pair_glue = $pair_glue;

            return $this;
        }
}
