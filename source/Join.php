<?php

namespace DibiActiveRecord;

/**
 * Object representing JOIN
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
class Join {

	/**
	 * In which way join external table?
	 * @var string
	 */
	protected $type = 'JOIN';

	/**
	 * Name of the table to connect
	 * @var string
	 */
	protected $table;

	/**
	 * Query local name of the table to connect
	 * @var string
	 */
	protected $name;

	/**
	 * Conditions to be passed to ON
	 * @var Condition[]
	 */
	protected $conditions = array();

	/**
	 * Constructs new JOIN
	 * @param string $table Name of the table to connect
	 * @param array $conditions Conditions passed to ON
	 * @param string $joinType In which way join external table?
	 */
	public function __construct($table, array $conditions = array(), $joinType = 'JOIN', $name = null) {
		$this->table = $table;
		$this->conditions = Condition::createListFrom($conditions);
		$this->type = (string)$joinType;
		$this->name = $name;
	}

	/**
	 * Apply this JOIN statement on given query
	 * @param array $query Query to apply JOIN on
	 */
	public function applyOn(array &$query) {
		$query[] = sprintf('%s %%n %%n', $this->type);
		$query[] = $this->table;
		$query[] = $this->name ?: $this->table;
		if(!empty($this->conditions)) {
			$query[] = 'ON';
			Condition::applyListOn($query, $this->conditions);
		}
	}
}