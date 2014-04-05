<?php

namespace DibiActiveRecord\Filter;

use DibiActiveRecord\Filter;

/**
 * Storing extended data as JSON
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @subpackage Filter
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class Json implements Filter
{

	/**
	 * Decodes data as array?
	 * @var boolean 
	 */
	public $asArray = true;

	/**
	 * Method called on setting variable to active record
	 * @param mixed $value Input variable
	 * @return string
	 */
	public function input($value) {
		return json_encode($value);
	}

	/**
	 * Method called on getting variable from active record
	 * @param string $value Field value
	 * @return mixed
	 */
	public function output($value) {
		return json_decode($value, $this->asArray);
	}

	/**
	 * Sets filter's internal setting according given variables
	 * @param mixed[] $settings Filter's settings
	 */
	public function setSettings(array $settings) {
		if(isset($settings['asArray'])) {
			$this->asArray = (boolean)$settings['asArray'];
		}
	}
}