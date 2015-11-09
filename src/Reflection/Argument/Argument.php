<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 06/11/2015
	 * Time: 16:06
	 */
	
	namespace Goblinoid\Util\Reflection\Argument;
	
	use Goblinoid\Util\Text\ReplaceVarsFromArray;

	abstract class Argument
	{
		private $name;

		private $position;

		private $has_default = false;

		private $default_value;

		/**
		 * @param $value
		 * @return mixed
		 *
		 * @return array Pair of [$result: bool, $error: string | null]
		 */
		abstract protected function isValid($value);

		/**
		 * @return mixed
		 */
		abstract protected function getTypeHint();

		public function __construct(\ReflectionParameter $parameter)
		{
			$this->name = $parameter->getName();
			$this->position = $parameter->getPosition();
			$this->has_default = $parameter->isDefaultValueAvailable();
			$this->default_value = $this->getHasDefault()
					? $parameter->getDefaultValue()
					: null;
		}

		public function getValueFromData($data)
		{
			if (array_key_exists($this->name, $data))
			{
				$value = $data[$this->name];
			}
			elseif (array_key_exists($this->position, $data))
			{
				$value = $data[$this->position];
			}
			elseif ($this->has_default)
			{
				$value = $this->getDefaultValue();
			}
			else
			{
				$template = new ReplaceVarsFromArray('{$name} in position {$position} is not set in $$data, and isn\'t optional');
				$message = $template([
					'name'     => $this->name,
					'position' => $this->position
				]);

				throw new \OutOfRangeException($message);
			}

			// If the parameter defaults to null and the data passed is also null we can skip
			// type checking and just assign null to the parameter
			if ($this->has_default && $this->default_value === null && $value === null)
			{
				return null;
			}

			list($is_valid, $error_message) = $this->isValid($value);

			if(!$is_valid)
			{
				throw new \InvalidArgumentException($error_message);
			}

			return $value;
		}

		public function __toString()
		{
			return implode(' ', array_filter([$this->getTypeHint(), '$' . $this->getName(), $this->has_default ? '=' : '', $this->has_default ? var_export($this->default_value, true) : '']));
		}

		public function getName()
		{
			return $this->name;
		}

		/**
		 * @return mixed
		 */
		public function getPosition()
		{
			return $this->position;
		}

		/**
		 * @return boolean
		 */
		public function getHasDefault()
		{
			return $this->has_default;
		}

		/**
		 * @return mixed
		 */
		public function getDefaultValue()
		{
			return $this->default_value;
		}

		protected function buildErrorMessage($is_valid, $template_part, $params = [])
		{
			if($is_valid)
			{
				return null;
			}

			$template = new ReplaceVarsFromArray('Invalid value for {$name}, it should be ' . $template_part);

			return $template(array_merge(['name' => $this->name], $params));
		}

	}
