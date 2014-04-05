<?php

namespace DibiActiveRecord\Validator;

/**
 * Checks if value is numeric
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @subpackage Validator
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class Numeric extends AValidator {

	/**
	 * Checks value and add errors if not valid
	 */
	protected function check() {
		if(!is_numeric($this->value)) {
			$this->addError('Attribute {name} has to be numeric');
		}
	}
}