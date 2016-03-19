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
 * Condition used in where/having clausules
 *
 * @category  ORM
 * @package   DibiActiveRecord
 * @author    Tomas Tatarko <tomas@tatarko.sk>
 * @copyright 2014 Tomas Tatarko
 * @license   http://choosealicense.com/licenses/mit/ The MIT License
 * @link      https://github.com/tatarko/dibi-active-record Official repository
 */
class Condition
{

    /**
     * String representing rule of the condition
     * @var string
     */
    protected $rule;

    /**
     * Params to be used
     * @var mixed[]
     */
    protected $params = array();

    /**
     * Operator to be used before this condition
     * @var string
     */
    protected $operator = 'AND';

    /**
     * Constructs new condition
     * @param type   $rule     String representing rule of the condition
     * @param array  $params   Params to be used
     * @param string $operator Operator to be used before this condition
     */
    public function __construct($rule, array $params = array(), $operator = 'AND') 
    {
        $this->rule = (string)$rule;
        $this->params = $params;
        $this->operator = (string)$operator;
    }

    /**
     * Gets operator used in condition
     * @return string
     */
    public function getOperator() 
    {
        return $this->operator;
    }

    /**
     * Create new condition from given value
     * @param mixed $condition String, array or Condition object
     * @return Condition
     * @throws Exception
     */
    public static function createFrom($condition) 
    {
        if ($condition instanceof self) {
            return $condition;
        } elseif (is_string($condition)) {
            return new self($condition);
        } elseif (is_array($condition) && count($condition)) {
            return new self(
                current($condition),
                isset($condition[1]) && is_array($condition[1])
                ? $condition[1]
                : array(),
                isset($condition[2]) ? $condition[2] : 'AND'
            );
        }
        throw new Exception('Unsupported condition value');
    }

    /**
     * Creating list of conditions from an array
     * @param array $conditions List of values for building conditions
     * @return Condition[]
     */
    public static function createListFrom(array $conditions) 
    {
        foreach ($conditions as $index => $condition) {
            $conditions[$index] = self::createFrom($condition);
        }
        return $conditions;
    }

    /**
     * Adds condition to given query
     * @param array $query Query to add condition in
     * @return void
     */
    public function applyOn(array &$query) 
    {
        $query[] = sprintf('(%s)', $this->rule);
        $query = array_merge($query, $this->params);
    }

    /**
     * Adds conditions to given query
     * @param array       $query Query to add condition in
     * @param Condition[] $list  List of conditions to apply on query
     * @return void
     */
    public static function applyListOn(array &$query, array $list) 
    {
        $counter = 0;
        foreach ($list as $condition) {
            if (!$condition instanceof self) {
                continue;
            }

            if ($counter++) {
                $query[] = $condition->getOperator();
            }
            
            $condition->applyOn($query);
        }
    }
}