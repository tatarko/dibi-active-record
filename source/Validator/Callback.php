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

namespace Tatarko\DibiActiveRecord\Validator;

use Tatarko\DibiActiveRecord\Exception;

/**
 * Checks value according to given callback
 *
 * @category   ORM
 * @package    DibiActiveRecord
 * @subpackage Validator
 * @author     Tomas Tatarko <tomas@tatarko.sk>
 * @copyright  2014 Tomas Tatarko
 * @license    http://choosealicense.com/licenses/mit/ The MIT License
 * @link       https://github.com/tatarko/dibi-active-record Official repository
 */
class Callback extends AValidator
{

    /**
     * Callback to check value
     * @var callable
     */
    public $callback;

    /**
     * Constructs validator
     * @param callable $callback Callable function or state to execute
     */
    public function __construct($callback = null) 
    {
        $this->callback = $callback;
    }

    /**
     * Checks value and add errors if not valid
     * @return void
     */
    protected function check() 
    {
        if (!is_callable($this->callback)) {
            throw new Exception('Given callback is not callable');
        }

        $callback = $this->callback;
        $callback($this);
    }
}