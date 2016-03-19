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
 * Checks if value does exists in the haystack
 *
 * @category   ORM
 * @package    DibiActiveRecord
 * @subpackage Validator
 * @author     Tomas Tatarko <tomas@tatarko.sk>
 * @copyright  2014 Tomas Tatarko
 * @license    http://choosealicense.com/licenses/mit/ The MIT License
 * @link       https://github.com/tatarko/dibi-active-record Official repository
 */
class In extends AValidator
{

    /**
     * Haystack to search needle in
     * @var string|array
     */
    public $haystack;

    /**
     * Checks value and add errors if not valid
     * @return void
     */
    protected function check() 
    {
        if (is_array($this->haystack)) {
            if (!in_array($this->value, $this->haystack)) {
                $this->addError('Value not allowed');
            }
            return;
        }

        if (mb_strpos($this->haystack, $this->value) === false) {
            $this->addError('Value not allowed');
        }
    }
}