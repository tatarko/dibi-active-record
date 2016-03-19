<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * PHP Version 5.3
 *
 * @category ORM
 * @package  DibiActiveRecord
 * @author   Tomáš Tatarko <tomas@tatarko.sk>
 * @license  http://choosealicense.com/licenses/mit/ MIT
 * @link     https://github.com/tatarko/dibi-active-record Official repository
 */

namespace Tatarko\DibiActiveRecord;

use ArrayObject;
use dibi;

/**
 * ActiveView built above dibi for querying views
 *
 * @category  ORM
 * @package   DibiActiveRecord
 * @author    Tomas Tatarko <tomas@tatarko.sk>
 * @copyright 2014 Tomas Tatarko
 * @license   http://choosealicense.com/licenses/mit/ The MIT License
 * @link      https://github.com/tatarko/dibi-active-record Official repository
 */
abstract class ActiveView extends ArrayObject
{
    const HAS_MANY = 'Tatarko\\DibiActiveRecord\\Relation\\HasMany';
    const BELONGS_TO = 'Tatarko\\DibiActiveRecord\\Relation\\BelongsTo';

    /**
     * Criteria for selecting records from database
     * @var Criteria
     */
    private $_criteria;

    /**
     * Stack of filters to use on setting/getting values
     * @var FilterInterface[]
     */
    private $_filters;

    /**
     * Crate for related objects
     * @var ActiveView[]
     */
    private $_related = array();

    /**
     * Static caching of table meta datas
     * @var array
     */
    private static $_tableMetaData = array();

    /**
     * Gets table/view name
     * @return string
     */
    public function tableName()
    {
        $classVector = explode('\\', get_called_class());
        return end($classVector);
    }

    /**
     * Gets name of primary key field
     * @return string
     */
    public function primaryKeyName()
    {
        return 'id';
    }

    /**
     * Gets model of the active record
     * @return ActiveView
     */
    public static function model()
    {
        $class = get_called_class();
        return new $class(array());
    }

    /**
     * Constructs new active record
     * @param array $array Default attributes for that record
     */
    public function __construct(array $array = array())
    {
        parent::__construct($array);
    }

    /**
     * Gets connection to database
     * @return \DibiConnection
     */
    public function getConnection()
    {
        return dibi::getConnection();
    }

    /**
     * Gets selecting criteria for current active record model
     * @return Criteria
     */
    public function getCriteria()
    {
        if ($this->_criteria) {
            return $this->_criteria;
        }
        return $this->_criteria = new Criteria($this);
    }

    /**
     * Sets criteria for selecting records from database
     * @param Criteria $criteria Instance of criteria object
     * @return void
     */
    public function setCriteria(Criteria $criteria)
    {
        $this->_criteria = $criteria;
    }

    /**
     * Requests data from database
     * @param Criteria $criteria Additional criteria for matching records
     * @return ActiveView[] List of active records fetched from database
     */
    protected function requestData(Criteria $criteria = null)
    {
        $mergedCriteria = $criteria ?: $this->getCriteria();
        $result = $this->getConnection()->query(
            $mergedCriteria->build($this->tableName())
        )->setRowClass(get_called_class());

        $relations = $mergedCriteria->getRelationList();
        if (empty($relations) || !count($result)) {
            return $result;
        }

        $data = $result->fetchAll();
        foreach ($this->relations() as $name => $meta) {
            if (in_array($name, $relations)) {
                $this->makeRelation($name, $meta)->searchFor($data, $name);
                unset($relations[array_search($name, $relations)]);
            }
        }

        if (!empty($relations)) {
            throw new Exception('One or more relations are not defined', 0);
        }
        
        return $data;
    }

    /**
     * Finds active record by primary key value
     * @param integer $id Primary key value to find record by
     * @return ActiveView
     */
    public function findByPk($id)
    {
        return $this->requestData(
            $this->getCriteria()->compare($this->primaryKeyName(), $id)
        )->fetch();
    }

    /**
     * Finds all records matching given criteria
     * @param Criteria $criteria Additional criteria to search records by
     * @return ActiveView[]
     */
    public function findAll(Criteria $criteria = null)
    {
        return $this->requestData(
            $criteria
            ? $this->getCriteria()->mergeWith($criteria)
            : $this->getCriteria()
        );
    }

    /**
     * Finds all records matching given criteria
     * @return ActiveView[]
     */
    public function find()
    {
        return $this->requestData($this->getCriteria()->limit(1))->fetch();
    }

    /**
     * Refresh variables to actual data from the database
     * @param integer $id Reference ID
     * @return ActiveView
     */
    public function refresh($id = null)
    {
        $this->exchangeArray(
            $this->requestData(
                $this->getCriteria()->compare(
                    $this->primaryKeyName(), $id ?: $this->{$this->primaryKeyName()}
                )
            )->fetch()->getArrayCopy()
        );
        return $this;
    }

    /**
     * Ziska attributy modely
     * @param array $names Filtrovanie len specifickych attributov
     * @return array
     */
    public function getAttributes(array $names = array()) 
    {
        foreach (array_keys($this->getArrayCopy()) as $name) {
            if (empty($names) || in_array($name, $names)) {
                $return[$name] = $this->getAttribute($name);
            }
        }
        return $return;
    }

    /**
     * Nahradi hodnoty attributov
     * @param array $attributes Nove hodnoty attributov
     * @return void
     */
    public function setAttributes(array $attributes) 
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
    }

    /**
     * Sets new attribute value
     * @param string $name  Attribute to set
     * @param mixed  $value Value to set
     * @return void
     */
    public function setAttribute($name, $value) 
    {
        $this->prepareFilters()->offsetSet(
            $name, isset($this->_filters[$name])
            ? $this->_filters[$name]->input($value)
            : $value
        );
    }

    /**
     * Gets new attribute value
     * @param string $name Attribute to get
     * @return mixed Attribute value
     */
    public function getAttribute($name)
    {
        if (key_exists($name, $this->_related)) {
            return $this->_related[$name];
        }

        $this->prepareFilters();
        if (!$this->offsetExists($name)) {
            foreach ($this->relations() as $relationName => $meta) {
                if ($relationName == $name) {
                    $this->makeRelation($relationName, $meta)
                        ->searchFor(array($this), $relationName);
                    return $this->_related[$name];
                }
            }
        }

        $value = $this->offsetGet($name);        
        return isset($this->_filters[$name])
        ? $this->_filters[$name]->output($value)
        : $value;
    }

    /**
     * Magic method for accessing properties of an object
     * @param string $name  Property name
     * @param mixed  $value Property value
     * @return void
     */
    public function __set($name, $value) 
    {
        $this->setAttribute($name, $value);
    }

    /**
     * Magic method for accessing properties of an object
     * @param string $name Property name
     * @return mixed Property value
     */
    public function __get($name) 
    {
        return $this->getAttribute($name);
    }

    /**
     * Gets list of filters meta data
     * @return array
     */
    public function filters() 
    {
        return array();
    }

    /**
     * Builds filter instances from meta data returned from `filters()` method
     * @return ActiveView Supports method chaining
     */
    protected function prepareFilters() 
    {
        if ($this->_filters !== null) {
            return $this;
        }

        $this->_filters = array();
        foreach ($this->filters() as $settings) {
            list($fields, $filter) = $settings;
            unset($settings[0], $settings[1]);

            if (!$filter instanceof FilterInterface) {
                $name = sprintf(__NAMESPACE__.'\\Filter\\%s', ucfirst($filter));
                $filter = new $name;
                $filter->setSettings($settings);
            }

            foreach (preg_split('#[\s,]+#', trim($fields)) as $field) {
                $this->setFilter(trim($field), $filter);
            }
        }

        return $this;
    }

    /**
     * Sets filter to requested column
     * @param string          $column Column name to set filter on
     * @param FilterInterface $filter Instance of filter
     * @return void
     */
    public function setFilter($column, FilterInterface $filter) 
    {
        $this->_filters[$column] = $filter;
    }

    /**
     * Gets list of object's relations
     * @return RelationAbstract[]
     */
    public function relations()
    {
        return array();
    }

    /**
     * Sets related object by its name
     * @param string     $name     Relation name to set record
     * @param ActiveView $record   Record model to set
     * @param boolean    $multiple Multiple records will be set
     * @return void
     * @internal This method is used by ORM mapper
     */
    public function setRelatedRecord($name, ActiveView $record, $multiple = false)
    {
        if (!$multiple) {
            $this->_related[$name] = $record;
            return null;
        }

        if (!key_exists($name, $this->_related)
            || !is_array($this->_related[$name])
        ) {
            $this->_related[$name] = array($record);
        } else {
            $this->_related[$name][] = $record;
        }
    }

    /**
     * Gets table meta data for current active record
     * @return array
     */
    public function getMetaData() 
    {
        if (key_exists($this->tableName(), self::$_tableMetaData)) {
            return self::$_tableMetaData[$this->tableName()];
        }

        $columns = array();
        $defaults = array();
        $defQuery = $this->getConnection()->query(
            'SHOW COLUMNS FROM %n',
            $this->tableName()
        );

        foreach ($defQuery as $column) {
            $columns[$column->Field] = $column->Field;
            $defaults[$column->Field] = $column->Default == 'CURRENT_TIMESTAMP'
            ? date('Y-m-d H:i:s') : $column->Default;
        }

        return self::$_tableMetaData[$this->tableName()] = array(
        'columns' => $columns,
        'attributeDefaults' => $defaults,
        'relations' => array_keys($this->relations()),
        );
    }

    /**
     * Makes instance of relation from its meta data
     * @param type  $name Relation name
     * @param array $meta Relation meta data
     * @return RelationAbstract
     * @throws Exception
     */
    protected function makeRelation($name, array $meta) 
    {
        if (!in_array(count($meta), array(3, 4))) {
            throw new Exception('Wrong parameter count for relation definition');
        }

        list($type, $table, $field, $criteria) = $meta + array(3 => null);
        if (!class_exists($type)) {
            throw new Exception("'{$type}' relation type does not exists");
        }

        return new $type($name, $table, $field, $criteria);
    }
}
