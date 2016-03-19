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

/**
 * Checks if value is string
 *
 * @category   ORM
 * @package    DibiActiveRecord
 * @subpackage Validator
 * @author     Tomas Tatarko <tomas@tatarko.sk>
 * @copyright  2014 Tomas Tatarko
 * @license    http://choosealicense.com/licenses/mit/ The MIT License
 * @link       https://github.com/tatarko/dibi-active-record Official repository
 */
class String extends AValidator
{

    /**
     * Maximal allowed string length
     * @var integer
     */
    public $maxLength;

    /**
     * Minimal allowed string length
     * @var integer
     */
    public $minLength;

    /**
     * Checks value and add errors if not valid
     * @return void
     */
    protected function check() 
    {
        if (!is_string($this->value)) {
            return $this->addError('Attribute {name} has to be string');
        }

        if ($this->maxLength && mb_strlen($this->value) > $this->maxLength) {
            $this->addError(
                sprintf(
                    'Attribute {name} can not be longer than %d letters',
                    $this->maxLength
                )
            );
        }

        if ($this->minLength && mb_strlen($this->value) < $this->minLength) {
            $this->addError(
                sprintf(
                    'Attribute {name} can not be shorter than %d letters',
                    $this->minLength
                )
            );
        }
    }
}