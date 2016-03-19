# Dibi Active Record

Active Record built upon [dibi](https://github.com/dg/dibi) Database Abstraction Library.

## Requirements

`DibiActiveRecord` requires to run correctly:

- `PHP`, version `5.3` or above

## Instalation

### Composer

Simply add a dependency on `tatarko/dibi-active-record` to your project's `composer.json` file if you use [Composer](http://getcomposer.org) to manage the dependencies of your project. Here is a minimal example of a `composer.json` file that just defines a dependency on Dibi Active Record:

```
{
	"require": {
		"tatarko/dibi-active-record": "^1.0"
	}
}
```

## Getting started

To use dibi active record you simply define model's class that extends common parent class ActiveRecord from this repository.

### Definying model's class

```
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

After we successfully define our first model, we can work with it in common way used in active record/orm pattern.

```
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

```
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

```
$model = new User();
var_dump($model->isNewRecord()); // true

$other = User::model()->findByPk(2);
var_dump($model->isNewRecord()); // false

$other->refresh(); // fetch current field values from db
```

