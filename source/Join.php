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
 * Object representing JOIN
 *
 * @category  ORM
 * @package   DibiActiveRecord
 * @author    Tomas Tatarko <tomas@tatarko.sk>
 * @copyright 2014 Tomas Tatarko
 * @license   http://choosealicense.com/licenses/mit/ The MIT License
 * @link      https://github.com/tatarko/dibi-active-record Official repository
 */
class Join
{

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
     * @param string $table      Name of the table to connect
     * @param array  $conditions Conditions passed to ON
     * @param string $joinType   In which way join external table?
     * @param string $name       Name of the table alias to use
     */
    public function __construct(
        $table,
        array $conditions = array(),
        $joinType = 'JOIN',
        $name = null
    ) {
        $this->table = $table;
        $this->conditions = Condition::createListFrom($conditions);
        $this->type = (string)$joinType;
        $this->name = $name;
    }

    /**
     * Apply this JOIN statement on given query
     * @param array $query Query to apply JOIN on
     * @return void
     */
    public function applyOn(array &$query) 
    {
        $query[] = sprintf('%s %%n %%n', $this->type);
        $query[] = $this->table;
        $query[] = $this->name ?: $this->table;
        if (!empty($this->conditions)) {
            $query[] = 'ON';
            Condition::applyListOn($query, $this->conditions);
        }
    }
}