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

namespace Tatarko\DibiActiveRecord\Relation;

use Tatarko\DibiActiveRecord\RelationAbstract;
use Tatarko\DibiActiveRecord\ActiveView;
use Tatarko\DibiActiveRecord\Criteria;

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
class HasMany extends RelationAbstract
{
    /**
     * Search for relations
     * @param ActiveView   $model Source model (for meta data)
     * @param ActiveView[] $set   Current data set
     * @return void
     */
    public function searchFor(ActiveView $model, array $set)
    {
        $ids = array();
        foreach ($set as $i => $record) {
            $ids[$record[$model->primaryKeyName()]] = $i;
        }

        $sample = $this->getInstanceOf($this->model);
        $sample->getCriteria()->in($this->attribute, array_keys($ids));

        if ($this->criteria instanceof Criteria) {
            $sample->getCriteria()->mergeWith($this->criteria);
        }

        foreach ($sample->findAll() as $r) {
            $key = $ids[$r[$this->attribute]];
            $set[$key]->setRelatedRecord($this->name, $r, true);
        }
    }
}
