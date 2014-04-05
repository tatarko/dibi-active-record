<?php

namespace DibiActiveRecord\Validator;

/**
 * Checks value according to given callback
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @subpackage Validator
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class Callback extends AValidator {

	/**
	 * Callback to check value
	 * @var callable
	 */
	public $callback;

	/**
	 * Constructs validator
	 * @param callable $callback
	 */
	public function __construct($callback = null) {
		$this->callback = $callback;
	}

	/**
	 * Checks value and add errors if not valid
	 */
	protected function check() {
		if(!is_callable($this->callback)) {
			throw new \dibiar\Exception('Given callback is not callable');
		}

		$callback = $this->callback;
		$callback($this);
	}
}