<?php

namespace DibiActiveRecord;

/**
 * Exception used in Dibi Active Record
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class Exception extends \DibiException {

	/**
	 * Constructs an exception above FrmException
	 * @param string $message Expcetion message
	 * @return Exception
	 */
	public function __construct($message) {
		return parent::__construct($message);
	}
}