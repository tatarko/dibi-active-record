<?php

namespace DibiActiveRecord\Validator;

/**
 * Force target fields to be filled to pass validator
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @subpackage Validator
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class Required extends AValidator {

	/**
	 * Allow value to not exists or to be null
	 * @var boolean
	 */
	public $allowEmpty = false;

	/**
	 * Checks value and add errors if not valid
	 */
	protected function check() {
		
	}
}