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

require_once __DIR__ . '/interfaces.php';

/**
 * ActiveRecord built above dibi
 *
 * @category  ORM
 * @package   DibiActiveRecord
 * @author    Tomas Tatarko <tomas@tatarko.sk>
 * @copyright 2014 Tomas Tatarko
 * @license   http://choosealicense.com/licenses/mit/ The MIT License
 * @link      https://github.com/tatarko/dibi-active-record Official repository
 */
abstract class ActiveRecord extends ActiveView
{
    /**
     * List of errors made by validation
     * @var string[]
     */
    protected $errors = array();

    /**
     * Is the record new?
     * @return boolean
     */
    public function isNewRecord() 
    {
        $pk = $this->primaryKeyName();
        return !$this->offsetExists($pk) || empty($this[$pk]);
    }

    /**
     * Saves record
     * @param boolean $validate Validates model before saving?
     * @return boolean
     */
    public function save($validate = true)
    {
        if ($validate && !$this->validate()) {
            return false;
        }

        if ($this->beforeSave() === false) {
            return false;
        }

        if ($this->isNewRecord()) {
            $result = $this->insert();
        } else {
            $result = $this->update();
        }

        if ($result) {
            $this->afterSave();
            return true;
        }

        return false;
    }

    /**
     * Inserts new record to the database
     * @return boolean
     */
    protected function insert() 
    {
        $query = $this->getConnection()->query(
            'INSERT INTO %n',
            $this->tableName(),
            $this->getArrayCopy()
        );

        if (!is_int($query) || empty($query)) {
            return false;
        }

        $this->refresh($this->getConnection()->insertId());
        return true;
    }

    /**
     * Updates record in the database
     * @return boolean
     */
    protected function update() 
    {
        return is_int(
            $this->getConnection()->query(
                'UPDATE %n',
                $this->tableName(),
                ' SET %a ',
                $this->getArrayCopy(),
                ' WHERE %n = %i',
                $this->primaryKeyName(),
                $this->{$this->primaryKeyName()}
            )
        );
    }

    /**
     * Deletes record from the database
     * @return boolean
     * @throws Exception
     */
    public function delete()
    {
        if ($this->isNewRecord()) {
            throw new Exception('Unable to delete new record');
        }

        if ($this->beforeDelete() === false) {
            return false;
        }

        if (is_int(
            $this->getConnection()->query(
                'DELETE FROM %n WHERE %n = %i',
                $this->tableName(),
                $this->primaryKeyName(),
                $this->{$this->primaryKeyName()}
            )
        )) {
            $this->afterDelete();
            return true;
        }

        return false;
    }

    /**
     * Gets list of validators meta data
     * @return array
     */
    public function validators() 
    {
        return array();
    }

    /**
     * Builds filter instances from meta data returned from `validators()` method
     * @return Validator[]
     */
    protected function prepareValidators()
    {
        $validators = array();

        foreach ($this->validators() as $settings) {
            list($fields, $validator) = $settings;
            unset($settings[0], $settings[1]);

            if (is_callable($validator)) {
                $validator = new validator\Callback($validator);
            }

            if (!$validator instanceof Validator) {
                $name = sprintf(
                    __NAMESPACE__.'\\Validator\\%s',
                    ucfirst($validator)
                );
                $validator = new $name;
                $validator->setSettings($settings);
            }

            foreach (explode(',', $fields) as $field) {
                $validators[trim($field)][$validator->getId()] = $validator;
            }
        }

        return $validators;
    }

    /**
     * Validates model
     * @return boolean
     */
    public function validate()
    {
        $this->errors = array();

        if ($this->beforeValidate() === false) {
            return false;
        }

        foreach ($this->prepareValidators() as $field => $validators) {
            $value = isset($this[$field]) ? $this[$field] : null;
            foreach ($validators as $validator) {
                $validator->setValue($value);
                if (!$validator->isValid()) {
                    $this->errors[$field] = array_merge(
                        isset($this->errors[$field])
                        ? $this->errors[$field] : array(),
                        str_replace('{name}', $field, $validator->getErrors())
                    );
                }
            }
        }

        $this->afterValidate();
        return empty($this->errors);
    }

    /**
     * Adds custom error to specific attribute
     * @param string $attribute Attribute name to add error for
     * @param string $message   Error's message
     * @return void
     */
    public function addError($attribute, $message)
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * List of errors made by validation
     * @return string[][]
     */
    public function getErrors() 
    {
        return $this->errors;
    }

    /**
     * Method triggered before saving an active record to the database.
     * @return mixed If this method returns `FALSE`, saving is interrupted
     */
    public function beforeSave()
    {
        return true;
    }

    /**
     * Method triggered after an active record is successfully saved to the database
     * @return void
     */
    public function afterSave()
    {
    }

    /**
     * Method triggered before validating an active record
     * @return mixed If this method returns `FALSE`, validation is interrupted
     */
    public function beforeValidate()
    {
        return true;
    }

    /**
     * Method triggered after validation of an active record is completed
     * @return void
     */
    public function afterValidate()
    {
    }

    /**
     * Method triggered before deleting an active record from the database.
     * @return mixed If this method returns `FALSE`, deleting is interrupted
     */
    public function beforeDelete()
    {
        return true;
    }

    /**
     * Method triggered after an active record is successfully deleted from the db
     * @return void
     */
    public function afterDelete()
    {
    }
}
