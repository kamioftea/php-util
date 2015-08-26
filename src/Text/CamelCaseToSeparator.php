<?php
namespace HTC\PhinxExtensions\Util\Text;

/**
 * Description of CamelCaseToSeparator
 *
 * @package       HTC
 * @copyright (c) 2014 High Tech Click
 * @author        Jeff Horton <jeff.horton@hightechclick.com>
 *
 */
class CamelCaseToSeparator
{
    protected $separator = ' ';

    public function __construct($separator = ' ')
    {
        $this->setSeparator($separator);
    }

    public function __invoke($value, $separator = null)
    {
        return $this->apply($value, $separator);
    }

    /**
     * Splits camelCase Words e.g. CSSBuyerAddress13HTMLTest => CSS Buyer Address 13 HTML Test
     *
     * @param string      $value
     * @param string|null $separator
     * @return string
     */
    public function apply($value, $separator = null)
    {
        $separator = $separator !== null ? $separator : $this->getSeparator();
        $quoted_separator = preg_quote($separator);

        return implode($separator, preg_split("/((?<=[^\\d$quoted_separator])(?=[\\d]))|((?<=[^A-Z$quoted_separator])(?=[A-Z]+))|((?<=[A-Z])(?=[A-Z][a-z]+))/", $value, null, PREG_SPLIT_NO_EMPTY));
    }

    public function getSeparator()
    {
        return $this->separator;
    }

    public function setSeparator($separator)
    {
        $this->separator = $separator;

        return $this;
    }
}
