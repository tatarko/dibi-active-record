<?php

namespace DibiActiveRecord\Validator;

use DibiActiveRecord\Validator;

/**
 * Abstract Validator
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @subpackage Validator
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
abstract class AValidator implements Validator
{

	/**
	 * Allow value to not exists or to be null
	 * @var boolean
	 */
	public $allowEmpty = true;

	/**
	 * Value to validate
	 * @var mixed
	 */
	protected $value;

	/**
	 * Has value already been validated?
	 * @var boolean
	 */
	private $_validated = false;

	/**
	 * Errors produced for given value
	 * @var string[]
	 */
	private $_errors = array();

	/**
	 * Checks value and add errors if not valid
	 */
	abstract protected function check();

	/**
	 * Validates attribute value
	 */
	private final function validate() {
		if($this->value === null && !$this->allowEmpty) {
			$this->addError('Attribute {name} can not be empty');
		}
		
		if(!($this->value === null && $this->allowEmpty)) {
			$this->check();
		}

		$this->_validated = true;
	}

	/**
	 * Adds an error for this value validation
	 * @param string $message Validation error's message
	 */
	public function addError($message) {
		$this->_errors[] = $message;
	}

	/**
	 * Gets errors that given value generates
	 * @return string[]
	 */
	public function getErrors() {
		if(!$this->_validated) {
			$this->validate();
		}
		return $this->_errors;
	}

	/**
	 * Checks if value is valid
	 * @return boolean
	 */
	public function isValid() {
		if(!$this->_validated) {
			$this->validate();
		}
		return empty($this->_errors);
	}

	/**
	 * Sets validator's internal setting according given variables
	 * @param mixed[] $settings Validator's settings
	 */
	public function setSettings(array $settings) {
		$reflection = new \ReflectionClass($this);
		$properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
		
		foreach($settings as $index => $value) {
			foreach($properties as $property) {
				if($property->name == $index) {
					$property->setValue($this, $value);
					continue 2;
				}
			}

			throw new \dibiar\Exception(sprintf(
				'Class "%s" does not have property "%s"!',
				get_called_class(),
				$index
			));
		}
	}

	/**
	 * Sets variable to compare
	 * @param mixed $value Variable
	 */
	public function setValue($value) {
		$this->value = $value;
		$this->_validated = false;
		$this->_errors = array();
	}

	/**
	 * Gets unique ID of the validator
	 * @return string
	 */
	public function getId() {
		return strtolower(trim(str_replace('\\', '.', get_called_class()), '.'));
	}

	/**
	 * Gets variable to compare
	 * $return mixed Variable
	 */
	public function getValue() {
		return $this->value;
	}
}