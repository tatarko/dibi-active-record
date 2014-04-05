<?php

namespace DibiActiveRecord;

/**
 * Filter for getting/setting properties
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
interface Filter {

	/**
	 * Method called on setting variable to active record
	 * @param mixed $value Input variable
	 * @return string
	 */
	public function input($value);

	/**
	 * Method called on getting variable from active record
	 * @param string $value Field value
	 * @return mixed
	 */
	public function output($value);

	/**
	 * Sets filter's internal setting according given variables
	 * @param mixed[] $settings Filter's settings
	 */
	public function setSettings(array $settings);
}

/**
 * Validator for values
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package dibiar
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
interface Validator {

	/**
	 * Sets variable to compare
	 * @param mixed $value Variable
	 */
	public function setValue($value);

	/**
	 * Checks if value is valid
	 * @return boolean
	 */
	public function isValid();

	/**
	 * Gets errors that given value generates
	 * @return string[]
	 */
	public function getErrors();

	/**
	 * Sets validator's internal setting according given variables
	 * @param mixed[] $settings Validator's settings
	 */
	public function setSettings(array $settings);

	/**
	 * Gets unique ID of the validator
	 * @return string
	 */
	public function getId();
}