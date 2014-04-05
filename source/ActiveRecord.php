<?php

namespace DibiActiveRecord;

require_once __DIR__ . '/interfaces.php';

/**
 * ActiveRecord built above dibi
 *
 * @author Tomas Tatarko <tomas.tatarko@websupport.sk>
 * @package DibiActiveRecord
 * @copyright Copyright 2014 Tomas Tatarko
 * @license http://choosealicense.com/licenses/mit/ The MIT License
 * @version 1.0
 * @since 1.0
 */
abstract class ActiveRecord extends \ArrayObject {

	/**
	 * Name of the primary key field
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * Name of the active dibi connection
	 * @var string
	 */
	protected $connection;

	/**
	 * List of errors made by validation
	 * @var string[]
	 */
	protected $errors = array();

	/**
	 * Stack of filters to use on setting/getting values
	 * @var Filter[]
	 */
	private $_filters;

	/**
	 * Stack of validators used to validate model
	 * @var Validator[]
	 */
	private $_validators;

	/**
	 * Criteria for selecting records from database
	 * @var Criteria
	 */
	private $_criteria;

	/**
	 * Gets table name
	 * @return string
	 */
	public function tableName() {
		$classVector = explode('\\', get_called_class());
		return end($classVector);
	}

	/**
	 * Gets model of the active record
	 * @return ActiveRecord
	 */
	public static function model() {
		$class = get_called_class();
		return new $class(array());
	}

	/**
	 * Constructs new active record
	 * @param array $array Default attributes for that record
	 */
	public function __construct(array $array = array()) {
		parent::__construct($array);
	}

	/**
	 * Gets connection to database
	 * @return \DibiConnection
	 */
	public function getConnection() {
		return \dibi::getConnection($this->connection);
	}

	/**
	 * Gets selecting criteria for current active record model
	 * @return Criteria
	 */
	public function getCriteria() {
		if($this->_criteria) {
			return $this->_criteria;
		}
		return $this->_criteria = new Criteria($this);
	}

	/**
	 * Sets criteria for selecting records from database
	 * @param Criteria $criteria Instance of criteria object
	 */
	public function setCriteria(Criteria $criteria) {
		$this->_criteria = $criteria;
	}

	/**
	 * Requests data from database
	 * @param Criteria $criteria
	 * @return ActiveRecord[] List of active records fetched from database
	 */
	protected function requestData(Criteria $criteria = null) {
		$criteria = $criteria ?: $this->getCriteria();
		return call_user_func_array(
			array(
				$this->getConnection(),
				'query'
			),
			$criteria->build($this->tableName())
		)->setRowClass(get_called_class());
	}

	/**
	 * Finds active record by primary key value
	 * @param integer $id
	 * @return ActiveRecord
	 */
	public function findByPk($id) {
		return $this->requestData(
			$this->getCriteria()->compare($this->primaryKey, $id)
		)->fetch();
	}

	/**
	 * Finds all records matching given criteria
	 * @param Criteria $criteria
	 * @return ActiveRecord[]
	 */
	public function findAll(Criteria $criteria = null) {
		return $this->requestData($criteria
			? $this->getCriteria()->mergeWith($criteria)
			: $this->getCriteria()
		);
	}

	/**
	 * Finds all records matching given criteria
	 * @param Criteria $criteria
	 * @return ActiveRecord[]
	 */
	public function find() {
		return $this->requestData($this->getCriteria()->limit(1))->fetch();
	}

	/**
	 * Is the record new?
	 * @return boolean
	 */
	public function isNewRecord() {
		return !$this->offsetExists($this->primaryKey) || empty($this[$this->primaryKey]);
	}

	/**
	 * Saves record
	 * @return boolean
	 */
	public function save($validate = true) {
		if($validate && !$this->validate()) {
			return false;
		}

		if($this->isNewRecord()) {
			return $this->insert();
		}

		return $this->update();
	}

	/**
	 * Inserts new record to the database
	 * @return boolean
	 */
	protected function insert() {
		$query = $this->getConnection()->query('INSERT INTO %n', $this->tableName(), $this->getArrayCopy());

		if(!is_int($query) || empty($query)) {
			return false;
		}

		$this->refresh($this->getConnection()->insertId());
		return true;
	}

	/**
	 * Updates record in the database
	 * @return boolean
	 */
	protected function update() {
		return is_int($this->getConnection()->query(
			'UPDATE %n',
			$this->tableName(),
			' SET %a ',
			$this->getArrayCopy(),
			' WHERE %n = %i',
			$this->primaryKey,
			$this->{$this->primaryKey}
		));
	}

	/**
	 * Refresh variables to actual data from the database
	 * @param integer $id Reference ID
	 * @return ActiveRecord
	 */
	public function refresh($id = null) {
		$this->exchangeArray(
			$this->requestData(
				$this->getCriteria()->compare($this->primaryKey, $id ?: $this->{$this->primaryKey})
			)->fetch()->getArrayCopy()
		);
		return $this;
	}

	/**
	 * Deletes record from the database
	 * @return boolean
	 * @throws ARException
	 */
	public function delete() {
		if($this->isNewRecord()) {
			throw new ARException('Unable to delete new record');
		}

		return is_int(
			$this->connection->query(
				'DELETE FROM %n WHERE %n = %i',
				$this->tableName(),
				$this->primaryKey,
				$this->{$this->primaryKey}
			)
		);
	}

	/**
	 * Ziska attributy modely
	 * @param array $names Filtrovanie len specifickych attributov
	 * @return array
	 */
	public function getAttributes(array $names = array()) {
		foreach(array_keys($this->getArrayCopy()) as $name) {
			if(empty($names) || in_array($name, $names)) {
				$return[$name] = $this->getAttribute($name);
			}
		}
		return $return;
	}

	/**
	 * Nahradi hodnoty attributov
	 * @param array $attributes Nove hodnoty attributov
	 */
	public function setAttributes(array $attributes) {
		foreach($attributes as $name => $value) {
			$this->setAttribute($name, $value);
		}
	}

	/**
	 * Sets new attribute value
	 * @param string $name Attribute to set
	 * @param mixed $value Value to set
	 */
	public function setAttribute($name, $value) {
		$this->prepareFilters()->offsetSet($name, isset($this->_filters[$name])
			? $this->_filters[$name]->input($value)
			: $value
		);
	}

	/**
	 * Gets new attribute value
	 * @param string $name Attribute to get
	 * @return mixed Attribute value
	 */
	public function getAttribute($name) {
		$this->prepareFilters();
		$value = $this->offsetGet($name);
		return isset($this->_filters[$name])
			? $this->_filters[$name]->output($value)
			: $value;
	}

	/**
	 * Magic method for accessing properties of an object
	 * @param string $name Property name
	 * @param mixed $value Property value
	 */
	public function __set($name, $value) {
		$this->setAttribute($name, $value);
	}

	/**
	 * Magic method for accessing properties of an object
	 * @param string $name Property name
	 * @return mixed Property value
	 */
	public function __get($name) {
		return $this->getAttribute($name);
	}

	/**
	 * Gets list of filters meta data
	 * @return array
	 */
	public function filters() {
		return array();
	}

	/**
	 * Gets list of validators meta data
	 * @return array
	 */
	public function validators() {
		return array();
	}

	/**
	 * Builds filter instances from meta data returned from `filters()` method
	 * @return ActiveRecord Supports method chaining
	 */
	protected function prepareFilters() {
		if($this->_filters !== null) {
			return $this;
		}

		$this->_filters = array();
		foreach($this->filters() as $settings) {
			list($fields, $filter) = $settings;
			unset($settings[0], $settings[1]);

			if(!$filter instanceof Filter) {
				$name = sprintf('\\DibiActiveRecord\\Filter\\%s', ucfirst($filter));
				$filter = new $name;
				$filter->setSettings($settings);
			}

			foreach(explode(',', $fields) as $field) {
				$this->setFilter(trim($field), $filter);
			}
		}

		return $this;
	}

	/**
	 * Sets filter to requested column
	 * @param string $column Column name to set filter on
	 * @param Filter $filter Instance of filter
	 */
	public function setFilter($column, Filter $filter) {
		$this->_filters[$column] = $filter;
	}

	/**
	 * Builds filter instances from meta data returned from `filters()` method
	 * @return ActiveRecord Supports method chaining
	 */
	protected function prepareValidators() {
		if($this->_validators !== null) {
			return $this;
		}

		$this->_validators = array();
		foreach($this->validators() as $settings) {
			list($fields, $validator) = $settings;
			unset($settings[0], $settings[1]);
			
			if(is_callable($validator)) {
				$validator = new validator\Callback($validator);
			}

			if(!$validator instanceof Validator) {
				$name = sprintf('\\DibiActiveRecord\\Validator\\%s', ucfirst($validator));
				$validator = new $name;
				$validator->setSettings($settings);
			}

			foreach(explode(',', $fields) as $field) {
				$this->addValidator(trim($field), $validator);
			}
		}

		return $this;
	}

	/**
	 * Adds new validator to the target column
	 * @param string $column Column name to add validator to
	 * @param Validator $validator Instance of validator
	 */
	public function addValidator($column, Validator $validator) {
		$this->_validators[$column][$validator->getId()] = $validator;
	}

	/**
	 * Validates model
	 * @return boolean
	 */
	public function validate() {
		$this->prepareValidators();
		$this->errors = array();
		$valid = true;

		foreach($this->_validators as $field => $validators) {
			$value = isset($this[$field]) ? $this[$field] : null;
			foreach($validators as $validator) {
				$validator->setValue($value);
				if(!$validator->isValid()) {
					$this->errors[$field] = array_merge(
						isset($this->errors[$field]) ? $this->errors[$field] : array(),
						str_replace('{name}', $field, $validator->getErrors())
					);
					$valid = false;
				}
			}
		}

		return $valid;
	}

	/**
	 * List of errors made by validation
	 * @return string[][]
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * Gets table meta data for current active record
	 * @return array
	 */
	public function getMetaData() {
		$columns = array();
		$defaults = array();

		foreach($this->getConnection()->query('SHOW COLUMNS FROM %n', $this->tableName()) as $column) {
			$columns[$column->Field] = $column->Field;
			$defaults[$column->Field] = $column->Default == 'CURRENT_TIMESTAMP' ? date('Y-m-d H:i:s') : $column->Default;
		}

		return array(
			'columns' => $columns,
			'attributeDefaults' => $defaults,
			'relations' => array(),
		);
	}
}