<?php

namespace DibiActiveRecord\Filter;

use DibiActiveRecord\Filter;

/**
 * Manipulating with data as php's DateTime object
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @subpackage Filter
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class Datetime implements Filter
{

	/**
	 * Method called on setting variable to active record
	 * @param mixed $value Input variable
	 * @return string
	 */
	public function input($value) {
		if($value instanceof \DateTime) {
			return $value->format('Y-m-d H:i:s');
		}
		elseif(isset($value['date'], $value['timezone']) && !is_numeric($value['timezone'])) {
			$value = new \DateTime($value['date'], new \DateTimeZone($value['timezone']));
			return $value->format('Y-m-d H:i:s');
		}
		return date('Y-m-d H:i:s', (int)$value);
	}

	/**
	 * Method called on getting variable from active record
	 * @param string $value Field value
	 * @return \DateTime
	 */
	public function output($value) {
		return new \DateTime($value);
	}

	/**
	 * Sets filter's internal setting according given variables
	 * @param mixed[] $settings Filter's settings
	 */
	public function setSettings(array $settings) {}
}