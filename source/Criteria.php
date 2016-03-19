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

/**
 * Criteria for selecting rows
 *
 * @category  ORM
 * @package   DibiActiveRecord
 * @author    Tomas Tatarko <tomas@tatarko.sk>
 * @copyright 2014 Tomas Tatarko
 * @license   http://choosealicense.com/licenses/mit/ The MIT License
 * @link      https://github.com/tatarko/dibi-active-record Official repository
 */
class Criteria
{

    /**
     * List of fields to group by
     * @var string[]
     */
    protected $group = array();

    /**
     * List of conditions to apply on HAVING clausule
     * @var Condition[]
     */
    protected $having = array();

    /**
     * List of JOINs
     * @var Join[]
     */
    protected $joins = array();

    /**
     * Number of rows to select from database
     * @var int
     */
    protected $limit;

    /**
     * Number of rows to skip before selecting from database
     * @var int
     */
    protected $offset;

    /**
     * List of fields to order by
     * @var string[]
     */
    protected $order = array();

    /**
     * List of fields to select
     * @var string
     */
    protected $select = 't.*';

    /**
     * List of conditions to apply on WHERE clausule
     * @var Condition[]
     */
    protected $where = array();

    /**
     * List of relation to fetch with model
     * @var string[]
     */
    protected $whit = array();

    /**
     * Limits number of rows to select by given number
     * @param integer $limit New limit
     * @return Criteria Instance of self for method chaining
     */
    public function limit($limit) 
    {
        $this->limit = max(1, (int)$limit);
        return $this;
    }

    /**
     * Limits number of rows to skip by given number
     * @param integer $offset New offset
     * @return Criteria Instance of self for method chaining
     */
    public function offset($offset) 
    {
        $this->offset = max(0, (int)$offset);
        return $this;
    }

    /**
     * Sets which fiedls to select
     * @param string $fields Comma-separated list of fields to select
     * @return Criteria Instance of self for method chaining
     */
    public function select($fields) 
    {
        $this->select = implode(', ', func_get_args());
        return $this;
    }

    /**
     * Sets field to order by
     * @param type $sorter Name of the field to order by
     * @return Criteria Instance of self for method chaining
     */
    public function orderBy($sorter) 
    {
        $this->order[] = $sorter;
        return $this;
    }

    /**
     * Sets field to group by
     * @param string $field Name of the field to group by
     * @return Criteria Instance of self for method chaining
     */
    public function groupBy($field) 
    {
        $this->group[] = $field;
        return $this;
    }

    /**
     * Makes RIGHT JOIN to table
     * @param string $table Name of the table to join
     * @param array  $on    List of conditions for joining data
     * @param string $name  Query local name of the table to connect
     * @return Criteria Instance of self for method chaining
     */
    public function rightJoin($table, array $on = array(), $name = null) 
    {
        $this->joins[] = new Join($table, $on, 'JOIN', $name);
        return $this;
    }

    /**
     * Makes RIGHT JOIN to table
     * @param string $table Name of the table to join
     * @param array  $on    List of conditions for joining data
     * @param string $name  Query local name of the table to connect
     * @return Criteria Instance of self for method chaining
     */
    public function leftJoin($table, array $on = array(), $name = null) 
    {
        $this->joins[] = new Join($table, $on, 'LEFT JOIN', $name);
        return $this;
    }

    /**
     * Adds a between condition to the condition property
     * @param string  $column   Name of the column to compare
     * @param integer $start    Minimal value to match
     * @param integer $end      Maximal value to match
     * @param boolean $onHaving Use this condition in HAVING clause instead of WHERE
     * @param string  $opeator  Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function between(
        $column,
        $start,
        $end,
        $onHaving = false,
        $opeator = 'AND'
    ) {
        $this->{$onHaving ? 'having' : 'where'}[] = new Condition(
            '%n BETWEEN %i AND %i',
            array($column, $start, $end),
            $opeator
        );
        return $this;
    }

    /**
     * Adds a less condition to the condition property
     * @param string  $column   Name of the column to compare
     * @param integer $value    Upper limit of the value
     * @param boolean $onHaving Use this condition in HAVING clause instead of WHERE
     * @param string  $opeator  Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function lessThan($column, $value, $onHaving = false, $opeator = 'AND') 
    {
        $this->{$onHaving ? 'having' : 'where'}[] = new Condition(
            '%n < %i',
            array($column, $value),
            $opeator
        );
        return $this;
    }

    /**
     * Adds a less or equal condition to the condition property
     * @param string  $column   Name of the column to compare
     * @param integer $value    Upper limit of the value
     * @param boolean $onHaving Use this condition in HAVING clause instead of WHERE
     * @param string  $opeator  Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function lessOrEqualThan(
        $column,
        $value,
        $onHaving = false,
        $opeator = 'AND'
    ) {
        $this->{$onHaving ? 'having' : 'where'}[] = new Condition(
            '%n <= %i',
            array($column, $value),
            $opeator
        );
        return $this;
    }

    /**
     * Adds a more condition to the condition property
     * @param string  $column   Name of the column to compare
     * @param integer $value    Lower limit of the value
     * @param boolean $onHaving Use this condition in HAVING clause instead of WHERE
     * @param string  $opeator  Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function moreThan($column, $value, $onHaving = false, $opeator = 'AND') 
    {
        $this->{$onHaving ? 'having' : 'where'}[] = new Condition(
            '%n > %i',
            array($column, $value),
            $opeator
        );
        return $this;
    }

    /**
     * Adds a more or equal condition to the condition property
     * @param string  $column   Name of the column to compare
     * @param integer $value    Lower limit of the value
     * @param boolean $onHaving Use this condition in HAVING clause instead of WHERE
     * @param string  $opeator  Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function moreOrEqualThan(
        $column,
        $value,
        $onHaving = false,
        $opeator = 'AND'
    ) {
        $this->{$onHaving ? 'having' : 'where'}[] = new Condition(
            '%n >= %i',
            array($column, $value),
            $opeator
        );
        return $this;
    }

    /**
     * Adds a comparison condition to the requested property
     * @param string  $column   Name of the column to compare
     * @param string  $value    Value to compare on requested field
     * @param boolean $onHaving Use this condition in HAVING clause instead of WHERE
     * @param string  $opeator  Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function compare($column, $value, $onHaving = false, $opeator = 'AND') 
    {
        $this->{$onHaving ? 'having' : 'where'}[] = new Condition(
            '%n = %s',
            array($column, $value),
            $opeator
        );
        return $this;
    }

    /**
     * Adds a search in stack on values condition to the requested property
     * @param string   $column   Name of the column to compare
     * @param string[] $values   Values to be searched in
     * @param boolean  $onHaving Use this condition in HAVING clause instead of WHERE
     * @param string   $opeator  Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function in($column, $values, $onHaving = false, $opeator = 'AND') 
    {
        $this->{$onHaving ? 'having' : 'where'}[] = new Condition(
            '%n IN %l',
            array($column, $values),
            $opeator
        );
        return $this;
    }

    /**
     * Adds a negation search in stack on values condition to the requested property
     * @param string   $column   Name of the column to compare
     * @param string[] $values   Values to be searched in
     * @param boolean  $onHaving Use this condition in HAVING clause instead of WHERE
     * @param string   $opeator  Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function notIn($column, $values, $onHaving = false, $opeator = 'AND') 
    {
        $this->{$onHaving ? 'having' : 'where'}[] = new Condition(
            '%n NOT IN %l',
            array($column, $values),
            $opeator
        );
        return $this;
    }

    /**
     * Adds a LIKE search condition to the requested property
     * @param string  $column   Name of the column to compare
     * @param string  $value    Value to search for
     * @param boolean $onHaving Use this condition in HAVING clause instead of WHERE
     * @param string  $opeator  Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function search($column, $value, $onHaving = false, $opeator = 'AND') 
    {
        $this->{$onHaving ? 'having' : 'where'}[] = new Condition(
            '%n LIKE %~like~',
            array($column, $value),
            $opeator
        );
        return $this;
    }

    /**
     * Adds new condition to WHERE clausule
     * @param string   $rule    Condition rule to use
     * @param string[] $params  Params for the conditions
     * @param string   $opeator Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function where($rule, array $params = array(), $opeator = 'AND') 
    {
        $this->where[] = new Condition(
            $rule,
            $params,
            $opeator
        );
        return $this;
    }

    /**
     * Adds new condition to HAVING clausule
     * @param string   $rule    Condition rule to use
     * @param string[] $params  Params for the conditions
     * @param string   $opeator Which operator use before this condition
     * @return Criteria Instance of self for method chaining
     */
    public function having($rule, array $params = array(), $opeator = 'AND') 
    {
        $this->having[] = new Condition(
            $rule,
            $params,
            $opeator
        );
        return $this;
    }

    /**
     * Builds query and selects data
     * @param string $table Table name to select data from
     * @return \DibiResult
     */
    public function build($table) 
    {
        $query = array(
        sprintf(
            'SELECT %s FROM %%n as %%n',
            $this->select
        ),
        $table,
        't'
        );

        foreach ($this->joins as $join) {
            $join->applyOn($query);
        }
        
        if (!empty($this->where)) {
            $query[] = 'WHERE';
            Condition::applyListOn($query, $this->where);
        }

        if (!empty($this->group)) {
            $query[] = 'GROUP BY ' . implode(', ', $this->group);
        }

        if (!empty($this->having)) {
            $query[] = 'HAVING';
            Condition::applyListOn($query, $this->having);
        }

        if (!empty($this->order)) {
            $query[] = 'ORDER BY ' . implode(', ', $this->order);
        }

        if (is_numeric($this->limit)) {
            $query[] = 'LIMIT ' . $this->limit;
        }

        if (is_numeric($this->offset)) {
            $query[] = 'OFFSET ' . $this->offset;
        }

        return $query;
    }

    /**
     * Merge current select criteria with another one
     * @param Criteria $criteria Another criteria to merge with
     * @return Criteria Instance of self for method chaining
     */
    public function mergeWith($criteria) 
    {
        if (is_array($criteria)) {
            $criteria = self::create($criteria);
        }

        if (!$criteria instanceof self) {
            throw new Exception('Not valid criteria to merge');
        }

        $class = new \ReflectionClass($this);
        foreach ($class->getProperties() as $property) {
            $name = $property->name;
            $property->setAccessible(true);

            if (in_array($name, array('select', 'limit', 'offset'))) {
                $this->$name = $property->getValue($criteria);
            } else {
                $this->$name = array_merge(
                    $this->$name,
                    $property->getValue($criteria)
                );
            }
        }

        return $this;
    }

    /**
     * Create instance of criteria by given array of properties
     * @param array $criteria List of properties to set on criteria's instance
     * @return Criteria
     * @throws \ReflectionException
     */
    public static function create(array $criteria) 
    {
        $instance = new self;
        $reflection = new \ReflectionClass($instance);

        foreach ($criteria as $name => $param) {

            if (in_array($name, array('having', 'where'))) {
                $param = self::castConditionsList($param);
            } elseif ($name == 'joins') {
                $param = self::castJoinsList($param);
            }

            $property = $reflection->getProperty($name);
            $property->setAccessible(true);
            $property->setValue($instance, $param);
        }
        
        return $instance;
    }

    /**
     * Cast array of join condition data into `Condition` instances
     * @param array $conditions Array of condition meta data
     * @return Condition[]
     */
    protected static function castConditionsList(array $conditions) 
    {
        $pattern = new Condition('');
        $class = new \ReflectionClass($pattern);
        $reflections = array();

        foreach ($conditions as $index => $properties) {
            $conditions[$index] = clone $pattern;

            foreach ($properties as $name => $value) {
                if (!isset($reflections[$name])) {
                    $reflections[$name] = $class->getProperty($name);
                    $reflections[$name]->setAccessible(true);
                }

                $reflections[$name]->setValue($conditions[$index], $value);
            }
        }

        return $conditions;
    }

    /**
     * Cast array of join meta data into `Join` instances
     * @param array $joins Array of join meta data
     * @return Join[]
     */
    protected static function castJoinsList(array $joins) 
    {
        $pattern = new Join('');
        $class = new \ReflectionClass($pattern);
        $reflections = array();

        foreach ($joins as $index => $properties) {
            $joins[$index] = clone $pattern;

            foreach ($properties as $name => $value) {
                if (!isset($reflections[$name])) {
                    $reflections[$name] = $class->getProperty($name);
                    $reflections[$name]->setAccessible(true);
                }

                $reflections[$name]->setValue(
                    $joins[$index],
                    $name == 'conditions'
                    ? self::castConditionsList($value)
                    : $value
                );
            }
        }
        
        return $joins;
    }

    /**
     * Fetch also related object(s) as well
     * @param string $relation Relation name to fetch with object
     * @return Criteria
     */
    public function whit($relation)
    {
        $this->whit[] = $relation;
        return $this;
    }

    /**
     * Gets list of relations to fetch
     * @return string[]
     */
    public function getRelationList()
    {
        return $this->whit;
    }
}
