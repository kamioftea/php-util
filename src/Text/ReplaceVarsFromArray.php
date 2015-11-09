<?php
    /**
     * Created by PhpStorm.
     * User: jeff
     * Date: 06/08/2015
     * Time: 14:25
     */

    namespace Goblinoid\Util\Text;

    class ReplaceVarsFromArray
    {
        private $template;
        private $allow_numerical_keys = false;

        public function __construct($template = null)
        {
            if ($template !== null)
            {
                $this->setTemplate($template);
            }
        }

        /**
         * @param array $array
         * @return string
         */
        public function __invoke(array $array)
        {
            return $this->apply($array);
        }

        /**
         * @param array $array
         * @return string
         */
        public function apply(array $array)
        {
            if ($this->getTemplate() === null)
            {
                throw new \BadMethodCallException('Template must be set before applying to an array');
            }

            $pattern = $this->getAllowNumericalKeys()
                ? '/(^\\h*)?(\\{)?(?<!\$)\\$(\\{)?(?P<name>[a-zA-Z0-9_\x7f-\xff]*)(?(2)\\}|(?(3)\\}|))/m'
                : '/(^\\h*)?(\\{)?(?<!\$)\\$(\\{)?(?P<name>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(?(2)\\}|(?(3)\\}|))/m';

            $replaced = preg_replace_callback(
                $pattern,
                function ($match) use ($array)
                {
                    if (!array_key_exists($match['name'], $array))
                    {
                        throw new \InvalidArgumentException("'{$match['name']}' was not present in the provided array");
                    }

                    list(, $indent) = $match;

                    return $indent . preg_replace('/\\h*(\\r\\n|\\r|\\n)/', '$1' . $indent, $array[$match['name']]);
                },
                $this->getTemplate()
            );

            return preg_replace('/\\$\\$/', '$', $replaced);
        }

        /**
         * @return string
         */
        public function getTemplate()
        {
            return $this->template;
        }

        /**
         * @param string $template
         * @return $this
         */
        public function setTemplate($template)
        {
            if (!is_string($template))
            {
                throw new \InvalidArgumentException('$template should be a string');
            }
            $this->template = $template;

            return $this;
        }

        /**
         * @return boolean
         */
        public function getAllowNumericalKeys()
        {
            return $this->allow_numerical_keys;
        }

        /**
         * @param boolean $allow_numerical_keys
         * @return $this
         */
        public function setAllowNumericalKeys($allow_numerical_keys = true)
        {
            $this->allow_numerical_keys = (bool) $allow_numerical_keys;

            return $this;
        }
    }
