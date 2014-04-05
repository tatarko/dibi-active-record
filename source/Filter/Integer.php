<?php

namespace DibiActiveRecord\Filter;

use DibiActiveRecord\Filter;

/**
 * Forcing values to integer
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @subpackage Filter
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class Integer implements Filter
{

	/**
	 * Method called on setting variable to active record
	 * @param mixed $value Input variable
	 * @return string
	 */
	public function input($value) {
		return (int)$value;
	}

	/**
	 * Method called on getting variable from active record
	 * @param string $value Field value
	 * @return \DateTime
	 */
	public function output($value) {
		return (int)$value;
	}

	/**
	 * Sets filter's internal setting according given variables
	 * @param mixed[] $settings Filter's settings
	 */
	public function setSettings(array $settings) {}
}