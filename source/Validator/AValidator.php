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

use ReflectionClass;
use ReflectionProperty;
use Tatarko\DibiActiveRecord\Validator;
use Tatarko\DibiActiveRecord\Exception;

/**
 * Abstract Validator
 *
 * @category   ORM
 * @package    DibiActiveRecord
 * @subpackage Validator
 * @author     Tomas Tatarko <tomas@tatarko.sk>
 * @copyright  2014 Tomas Tatarko
 * @license    http://choosealicense.com/licenses/mit/ The MIT License
 * @link       https://github.com/tatarko/dibi-active-record Official repository
 */
abstract class AValidator implements Validator
{
    /**
     * Allow value to not exists or to be null
     * @var boolean
     */
    public $allowEmpty = true;

    /**
     * Value to validate
     * @var mixed
     */
    protected $value;

    /**
     * Has value already been validated?
     * @var boolean
     */
    private $_validated = false;

    /**
     * Errors produced for given value
     * @var string[]
     */
    private $_errors = array();

    /**
     * Checks value and add errors if not valid
     * @return void
     */
    abstract protected function check();

    /**
     * Validates attribute value
     * @return void
     */
    private final function _validate() 
    {
        if ($this->value === null && !$this->allowEmpty) {
            $this->addError('Attribute {name} can not be empty');
        }
        
        if (!($this->value === null && $this->allowEmpty)) {
            $this->check();
        }

        $this->_validated = true;
    }

    /**
     * Adds an error for this value validation
     * @param string $message Validation error's message
     * @return void
     */
    public function addError($message) 
    {
        $this->_errors[] = $message;
    }

    /**
     * Gets errors that given value generates
     * @return string[]
     */
    public function getErrors() 
    {
        if (!$this->_validated) {
            $this->_validate();
        }
        return $this->_errors;
    }

    /**
     * Checks if value is valid
     * @return boolean
     */
    public function isValid() 
    {
        if (!$this->_validated) {
            $this->_validate();
        }
        return empty($this->_errors);
    }

    /**
     * Sets validator's internal setting according given variables
     * @param mixed[] $settings Validator's settings
     * @return void
     */
    public function setSettings(array $settings) 
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        
        foreach ($settings as $index => $value) {
            foreach ($properties as $property) {
                if ($property->name == $index) {
                    $property->setValue($this, $value);
                    continue 2;
                }
            }

            throw new Exception(
                sprintf(
                    'Class "%s" does not have property "%s"!',
                    get_called_class(),
                    $index
                )
            );
        }
    }

    /**
     * Sets variable to compare
     * @param mixed $value Variable
     * @return void
     */
    public function setValue($value) 
    {
        $this->value = $value;
        $this->_validated = false;
        $this->_errors = array();
    }

    /**
     * Gets unique ID of the validator
     * @return string
     */
    public function getId() 
    {
        return strtolower(trim(str_replace('\\', '.', get_called_class()), '.'));
    }

    /**
     * Gets variable to compare
     * @return mixed Variable to validate
     */
    public function getValue() 
    {
        return $this->value;
    }
}