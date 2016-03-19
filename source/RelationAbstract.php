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
 * Relation for mapping objects
 *
 * @category   ORM
 * @package    DibiActiveRecord
 * @subpackage Relation
 * @author     Tomas Tatarko <tomas@tatarko.sk>
 * @copyright  2014 Tomas Tatarko
 * @license    http://choosealicense.com/licenses/mit/ The MIT License
 * @link       https://github.com/tatarko/dibi-active-record Official repository
 */
abstract class RelationAbstract
{
    /**
     * Model name of related table
     * @var string
     */
    protected $model;

    /**
     * Attribute used for matching models
     * @var string
     */
    protected $attribute;

    /**
     * Additional search criteria
     * @var Criteria
     */
    protected $criteria;

    /**
     * Constructing new relation instance
     * @param string   $model     Relation's target model
     * @param string   $attribute Target's table attribute name
     * @param Criteria $criteria  Additional criteria
     */
    public function __construct($model, $attribute, Criteria $criteria = null)
    {
        $this->model = $model;
        $this->attribute = $attribute;
        $this->criteria = $criteria;
    }

    /**
     * Searching for relations
     * @param ActiveView[] $set  Set od records to search relations for
     * @param string       $name Relation name to set matched records to
     * @return void
     */
    abstract public function searchFor(array $set, $name);

    /**
     * Gets instance of model by name
     * @param string $model Name of the model to make
     * @return ActiveView
     */
    protected function getInstanceOf($model)
    {
        if (!class_exists($model)) {
            throw new Exception('Relation model "'.$model.'" does not exist');
        }
        return new $model();
    }
}
