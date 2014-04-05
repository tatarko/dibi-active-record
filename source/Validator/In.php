<?php

namespace DibiActiveRecord\Validator;

/**
 * Checks if value does exists in the haystack
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @subpackage Validator
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class In extends AValidator {

	/**
	 * Haystack to search needle in
	 * @var string|array
	 */
	public $haystack;

	/**
	 * Checks value and add errors if not valid
	 */
	protected function check() {
		if(is_array($this->haystack)) {
			if(!in_array($this->value, $this->haystack)) {
				$this->addError('Value not allowed');
			}
			return;
		}

		if(mb_strpos($this->haystack, $this->value) === false) {
			$this->addError('Value not allowed');
		}
	}
}