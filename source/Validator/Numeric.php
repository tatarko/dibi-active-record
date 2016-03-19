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

use Tatarko\DibiActiveRecord\ValidatorAbstract;

/**
 * Checks if value is numeric
 *
 * @category   ORM
 * @package    DibiActiveRecord
 * @subpackage Validator
 * @author     Tomas Tatarko <tomas@tatarko.sk>
 * @copyright  2014 Tomas Tatarko
 * @license    http://choosealicense.com/licenses/mit/ The MIT License
 * @link       https://github.com/tatarko/dibi-active-record Official repository
 */
class Numeric extends ValidatorAbstract
{

    /**
     * Checks value and add errors if not valid
     * @return void
     */
    protected function check() 
    {
        if (!is_numeric($this->value)) {
            $this->addError('Attribute {name} has to be numeric');
        }
    }
}