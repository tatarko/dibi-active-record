# Dibi Active Record

Active Record built upon [dibi](https://github.com/dg/dibi) Database Abstraction Library.

## Requirements

`DibiActiveRecord` requires to run correctly:

- `PHP`, version `5.3` or above

## Instalation

### Composer

Simply add a dependency on `tatarko/dibi-active-record` to your project's `composer.json` file if you use [Composer](http://getcomposer.org) to manage the dependencies of your project. Here is a minimal example of a `composer.json` file that just defines a dependency on Dibi Active Record:

```json
{
	"require": {
		"tatarko/dibi-active-record": "^1.0"
	}
}
```

## Getting started

To use dibi active record you simply define model's class that extends common parent class ActiveRecord from this repository.

### Definying model's class

```php
use Tatarko\DibiActiveRecord\ActiveRecord;

/**
 * Dynamic properties for accessing fields of table row
 * @property integer $id
 * @property string $name
 */
class User extends ActiveRecord
{
}
```

### Basic manipulation with model

After we have successfully defined our first model, we can work with it in common way used in active record/orm patterns.

```php
$model = new User();
$model->name = 'demo';

if($model->save()) { // insert new row to the table
	echo $model->id; // autoincrement values automatically set to model's attribute
	$model->delete();
}

$other = User::model()->findByPk(2); // by primary key (id field)

if($other) {
	$other->name = 'another test';
	$other->save(); // updates row in the table
}

foreach(User::model()->findAll() as $model) {
	// getting&iterating all rows from table
}
```

### Different table/primary key names

By default, name of the active record's table equals to class name without namespace prefix and primary key name equals to `id`. In case that you want to change it, you can override `tableName()` and `primaryKeyName()` methods:

```php
use Tatarko\DibiActiveRecord\ActiveRecord;

/**
 * Dynamic properties for accessing fields of table row
 * @property integer $user_id
 * @property string $name
 */
class User extends ActiveRecord
{
	public function tableName()
	{
		return 'users';
	}
	
	public function primaryKeyName()
	{
		return 'user_id';
	}
}
```

### Usefull methods

```php
$model = new User();
var_dump($model->isNewRecord()); // true

$other = User::model()->findByPk(2);
var_dump($other->isNewRecord()); // false

$other->refresh(); // fetch current field values from db
```

### Searching for rows

```php
// Getting first row that matches criteria
$model = User::model()->find();

// Specifying criteria for model searching
$model = User::model();
$criteria = $model->getCriteria()->search('name', 'dibi'); // search means `LIKE "%dibi%"
foreach($model->findAll() as $record) {
	var_dump($record);
}
```

### More searching criteria

- `$criteria->limit(integer $limit)`
	- limits number of rows to fetch
- `$criteria->offset(integer $offset)`
	- sets numbers of rows to skip on fetching
- `$criteria->select(string ...$fields)`
	- which fields to fetch

Following methods can be called more multiple times - their effect is combined:

- `$criteria->orderBy(string $sorter)`
	- how to order results
- `$criteria->groupBy(string $field)`
	- according to which columns should be results grouped
- `$criteria->rightJoin(string $table, array $on = array(), string $name = null)`
	- joins table `$table` by conditions from `$on` with type of `RIGHT JOIN`.
	- in case that `$name` is specified then additional table is joined under this local name
	- List `$on` can be filled with strings of partial conditions or arrays containing following elements:
		- string `rule` *(required)* - condition pattern
		- array `params` *(optional)* - parameters of that condition (in case that condition has placeholders such as `%i`, `%s` etc)
		- string `opetator` *(optional)* - operator to be used before current condition (if it is not the first), `AND` by default
- `$criteria->leftJoin(string $table, array $on = array(), string $name = null)`
	- joins table `$table` by conditions from `$on` with type of `LEFT JOIN`.
	- in case that `$name` is specified then additional table is joined under this local name
	- List `$on` can be filled with strings of partial conditions or arrays containing following elements:
		- string `rule` *(required)* - condition pattern
		- array `params` *(optional)* - parameters of that condition (in case that condition has placeholders such as `%i`, `%s` etc)
		- string `opetator` *(optional)* - operator to be used before current condition (if it is not the first), `AND` by default
- `$criteria->mergeWith($criteria)`
	- merges current criteria with another one
	- `$criteria` can be instance of `Tatarko\DibiActiveRecord\Criteria` or array of values for building `Tatarko\DibiActiveRecord\Criteria`
- `$criteria->where(string $rule, array $params = array(), string $opeator = 'AND')`
	- adds new condition to `WHERE` statement
	- `$params` represents parameters for that condition (in case that condition has placeholders such as  `%i`, `%s` etc)
	- `$operator` operator to be used before current condition (if it is not the first), `AND` by default
- `$criteria->having(string $rule, array $params = array(), string $opeator = 'AND')`
	- adds new condition to `HAVING` statement
	- `$params` represents parameters for that condition (in case that condition has placeholders such as  `%i`, `%s` etc)
	- `$operator` operator to be used before current condition (if it is not the first), `AND` by default

Following methods creates specified case of condtions. Argument `$onHaving` decides whether condition will be added to the `WHERE` statement (on `false` value) or to the `HAVING` statement (on `true` value). Argument `$operator` represents operator to be used before current condition (if it is not the first), `AND` by default. All the methods can be called multiple times with their effect to be combined.

- `$criteria->compare(string $column, string $value, boolean $onHaving = false, string $opeator = 'AND')`
	- Value in column `$column` has to equal to `$value`
- `$criteria->search(string $column, string $value, boolean $onHaving = false, string $opeator = 'AND')`
	- Column `$column` will searched for `$value` using `LIKE`
- `$criteria->between(string $column, float $start, float $end, boolean $onHaving = false, string $opeator = 'AND')`
	- Value of the `$column` has to be from interval `< $start; $end >`
- `$criteria->in(string $column, array $values, boolean $onHaving = false, string $opeator = 'AND')`
	- Column `$column` has to equal to at least one of `$values`
- `$criteria->notIn(string $column, array $values, boolean $onHaving = false, string $opeator = 'AND')`
	- Column `$column` must not equal to any value from `$values`
- `$criteria->lessThan(string $column, float $value, boolean $onHaving = false, string $opeator = 'AND')`
	- Value of `$column` has to be lower than `$value`
- `$criteria->lessOrEqualThan(string $column, float $value, boolean $onHaving = false, string $opeator = 'AND')`
	- Value of `$column` has to be lower than or equals to `$value`
- `$criteria->moreThan(string $column, float $value, boolean $onHaving = false, string $opeator = 'AND')`
	- Value of `$column` has to be higher than `$value`
- `$criteria->moreOrEqualThan(string $column, float $value, boolean $onHaving = false, string $opeator = 'AND')`
	- Value of `$column` has to be higher than or equals to `$value`

### Filters

Filters for Dibi Active Record is something like dynamic setter/getter for model's attribute. It can be defined by overriding `filters()` method.

```php
use Tatarko\DibiActiveRecord\ActiveRecord;

/**
 * @property integer $id
 * @property DateTime $created
 * @property DateTime $updated
 * @property array $jsonData
 */
class User extends ActiveRecord
{
    public function filters()
    {
        return array(
            array('jsonData', 'json'), // field will be on-the-fly encoded/decoded as json
            array('createTime,updateTime', 'datetime'), // mysql timestamp field - will be interpreted as DateTime object
        );
    }
}
```

Filters can used when getting model's attributes as object properties. If attributes are accessed as array elements then value is returned in its raw format.

```php
$model = User::model()->find();
var_dump(
    $model->jsonData, // array('index' => 'value');
    $model['jsonData'] // string '{"index":"value"}'
);
```

#### List of pre-defined filters

- `boolean` - value is interpreted as boolean and stored as 0/1 in database
- `datetime` - value is interpreted as `DateTime` object and stored as date/time in database
- `float` - value is interpreted as float
- `integer` - value is interpreted as integer
- `json` - field will be on-the-fly encoded/decoded as json

#### Custom filters

New filter can be created by implementing `Tatarko\DibiActiveRecord\FilterInterface` interface and defiying it `filters()` method.

```php
use Tatarko\DibiActiveRecord\FilterInterface;
use Tatarko\DibiActiveRecord\ActiveRecord;

class MyCustomFilter implementes FilterInterface
{
	// interface implementation
}

/**
 * @property integer $id
 * @property mixed $specialField
 */
class User extends ActiveRecord
{
    public function filters()
    {
        return array(
            array('specialField', new MyCustomFilter()),
        );
    }
}
```

### Validators

For validating model's attributes before inserting/updating to the database it is possible to set of validation rules to be performing before each saving operation. It can be achieved by overriding `validators()` method.

```php
use Tatarko\DibiActiveRecord\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 * @property string $group
 */
class User extends ActiveRecord
{
    public function validators()
    {
        return array(
            array('name,group', 'string'),
            array('group', 'in', 'haystack' => array('visitor', 'admin')),
            array('name', function($validator) {
            	// callback validator
            	// value to validate can be accessed by $validator->getValue()
            	// and store error in case of invalid value using:
				$validator->addError('Invalid name: '.$validator->getValue());
            }),
        );
    }
}
```

#### List of pre-defined validators

- `callback`
	- checks the value with given callback function
	- must to have parameter `callback` that must to be callable type
	- can have parameter `allowEmpty` which decides whether empty value is valid, `true` by default
- `in`
	- value musts equal to at least one of values in `haystack` that is required parameter
	- if `haystack` is array, then `in_array` is used, `mb_strpos` otherwise
	- can have parameter `allowEmpty` which decides whether empty value is valid, `true` by default
- `numeric`
	- checks for numeric type of value
	- can have parameter `allowEmpty` which decides whether empty value is valid, `true` by default
- `required`
	- checks for non-empty value
- `string`
	- checks for string type of value
	- can have parameter `minLength` - minimal string length
	- can have parameter `maxLength` - maximal string length
	- can have parameter `allowEmpty` which decides whether empty value is valid, `true` by default

#### Validation in action

```php
$model = new User();
$model->name = 'Some name';
$model->group = 'admin';

var_dump(
	$model->validate(), // false
	$model->getError(), // array('name => array('Invalid name: Some name'))
);
```

Validation is automatically triggered on model saving. In case that model is not valid, saving process is stopped. Validation can be skipped by filling `$validate` argument - `$model->save(false)`

#### Custom validators

New validator can be created by creating new class that extends `Tatarko\DibiActiveRecord\ValidatorAbstract`.

```php
use Tatarko\DibiActiveRecord\ValidatorAbstract;
use Tatarko\DibiActiveRecord\ActiveRecord;

class MyCustomValidator extends ValidatorAbstract
{
	// abstract methods implementation
}

/**
 * @property integer $id
 * @property mixed $specialField
 */
class User extends ActiveRecord
{
    public function filters()
    {
        return array(
            array('specialField', new MyCustomValidator()),
        );
    }
}
```

### Relations

In case that multiple tables are connected using foreign keys, thier records records can be mapped in Dibi Active Record as properties. Relations can be defined in `relations()` method.


```php
use Tatarko\DibiActiveRecord\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 * @property Service[] $services
 */
class User extends ActiveRecord
{
    public function relations()
    {
        return array(
        	'services' => array(self::HAS_MANY, 'Service', 'user_id'),
        );
    }
}

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property User $owner
 */
class User extends ActiveRecord
{
    public function relations()
    {
        return array(
        	'owner' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }
}
```

foreach(User::model()->findAll() as $model) {
	foreach($model->services as $service) {
		echo "User {$model->name} has service {$service->name}\n";
	}
}

$service = Service::model()->findByPk(123);
echo "Onwer of {$service->name} service is {$service->owner->name}\n";
```

### Events
In every model you can override following methods that will be triggered on specific occasions. In case of  before... events, the process itself can be stopped if event method returns `false`.

- `beforeValidate` is called before model validation
- `afterValidate` is called after performing model validation
- `beforeSave` is called before saving model to the DB
- `afterSave` is called as soon as model is successfully saved to the DB
- `beforeDelete` is called before model is deleted from DB
- `afterDelete` is called as soon as model is successfully deleted from the DB

### ActiveFinder

Class `ActiveFinder` (which is ancestor for `ActiveRecord`) is used for working with DB views. It shares common logic with `ActiveRecord` except of saving/deleting rows, validating and events.