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

namespace Tatarko\DibiActiveRecord\Filter;

use Tatarko\DibiActiveRecord\FilterInterface;

/**
 * Forcing values to float
 *
 * @category   ORM
 * @package    DibiActiveRecord
 * @subpackage Filter
 * @author     Tomas Tatarko <tomas@tatarko.sk>
 * @copyright  2014 Tomas Tatarko
 * @license    http://choosealicense.com/licenses/mit/ The MIT License
 * @link       https://github.com/tatarko/dibi-active-record Official repository
 */
class Float implements FilterInterface
{

    /**
     * Method called on setting variable to active record
     * @param mixed $value Input variable
     * @return string
     */
    public function input($value) 
    {
        return (float)$value;
    }

    /**
     * Method called on getting variable from active record
     * @param string $value Field value
     * @return float
     */
    public function output($value) 
    {
        return (float)$value;
    }

    /**
     * Sets filter's internal setting according given variables
     * @param mixed[] $settings Filter's settings
     * @return void
     */
    public function setSettings(array $settings) 
    {
    }
}