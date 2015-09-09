<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 09/09/2015
	 * Time: 10:59
	 */
	
	namespace Goblinoid\Util\Text;
	
	class ArrayAsAlignedString
	{
		/**
		 * Returns the array as a multi line string of key: value, with padding to align the values
		 *
		 * @param  array $array
		 * @return string
		 */
		public function apply(array $array)
		{
			$max_key = array_reduce(array_keys($array), function($acc, $val){return max($acc, mb_strlen($val));},0);
			$output = [];
			foreach($array as $key => $value)
			{
                // suffix new lines with spaces to indent by max key + 2 (for ': ')
				$indented_value = implode(PHP_EOL . str_repeat(' ', $max_key + 2), explode(PHP_EOL,$value));
				$output[] = $key . ':' . str_repeat(' ', $max_key - mb_strlen($key) + 1) . $indented_value;
			}

			return implode(PHP_EOL, $output);
		}

	}