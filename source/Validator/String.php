<?php

namespace DibiActiveRecord\Validator;

/**
 * Checks if value is string
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @subpackage Validator
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class String extends AValidator {

	/**
	 * Maximal allowed string length
	 * @var integer
	 */
	public $maxLength;

	/**
	 * Minimal allowed string length
	 * @var integer
	 */
	public $minLength;

	/**
	 * Checks value and add errors if not valid
	 */
	protected function check() {
		if(!is_string($this->value)) {
			$this->addError('Attribute {name} has to be string');
			return false;
		}

		if($this->maxLength && mb_strlen($this->value) > $this->maxLength) {
			$this->addError(sprintf('Attribute {name} can not be longer than %d letters', $this->maxLength));
		}

		if($this->minLength && mb_strlen($this->value) < $this->minLength) {
			$this->addError(sprintf('Attribute {name} can not be shorter than %d letters', $this->minLength));
		}
	}
}