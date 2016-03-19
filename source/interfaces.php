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
 * Filter for getting/setting properties
 *
 * @category  ORM
 * @package   DibiActiveRecord
 * @author    Tomas Tatarko <tomas@tatarko.sk>
 * @copyright 2014 Tomas Tatarko
 * @license   http://choosealicense.com/licenses/mit/ The MIT License
 * @link      https://github.com/tatarko/dibi-active-record Official repository
 */
interface Filter
{
    /**
     * Method called on setting variable to active record
     * @param mixed $value Input variable
     * @return string
     */
    public function input($value);

    /**
     * Method called on getting variable from active record
     * @param string $value Field value
     * @return mixed
     */
    public function output($value);

    /**
     * Sets filter's internal setting according given variables
     * @param mixed[] $settings Filter's settings
     * @return void
     */
    public function setSettings(array $settings);
}

/**
 * Validator for values
 *
 * @category  ORM
 * @package   DibiActiveRecord
 * @author    Tomas Tatarko <tomas@tatarko.sk>
 * @copyright 2014 Tomas Tatarko
 * @license   http://choosealicense.com/licenses/mit/ The MIT License
 * @link      https://github.com/tatarko/dibi-active-record Official repository
 */
interface Validator
{
    /**
     * Sets variable to compare
     * @param mixed $value Variable
     * @return void
     */
    public function setValue($value);

    /**
     * Checks if value is valid
     * @return boolean
     */
    public function isValid();

    /**
     * Gets errors that given value generates
     * @return string[]
     */
    public function getErrors();

    /**
     * Sets validator's internal setting according given variables
     * @param mixed[] $settings Validator's settings
     * @return void
     */
    public function setSettings(array $settings);

    /**
     * Gets unique ID of the validator
     * @return string
     */
    public function getId();
}

/**
 * ORM Relations
 *
 * @category  ORM
 * @package   DibiActiveRecord
 * @author    Tomas Tatarko <tomas@tatarko.sk>
 * @copyright 2014 Tomas Tatarko
 * @license   http://choosealicense.com/licenses/mit/ The MIT License
 * @link      https://github.com/tatarko/dibi-active-record Official repository
 */
interface Relation
{
    /**
     * Constructing new relation instance
     * @param string   $name      Relation name
     * @param string   $model     Relation's target model
     * @param string   $attribute Target's table attribute name
     * @param Criteria $criteria  Additional criteria to apply on searching
     */
    public function __construct(
        $name,
        $model,
        $attribute,
        Criteria $criteria = null
    );

    /**
     * Gets relation name
     * @return string
     */
    public function getName();

    /**
     * Searching for relations
     * @param ActiveView $model Base model for mapping
     * @param array      $set   Set od records to search relations for
     * @return void
     */
    public function searchFor(ActiveView $model, array $set);
}
