# Laravel Model unique identifier (MUID)

[comment]: <> ([![Total Downloads]&#40;https://img.shields.io/packagist/dt/rawaby88/muid.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/rawaby88/muid&#41;)
[comment]: <> (<img alt="PHP from Packagist" src="https://img.shields.io/packagist/php-v/rawaby88/muid.svg">)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rawaby88/muid.svg?style=flat-square)](https://packagist.org/packages/rawaby88/muid)
![GitHub Actions](https://github.com/rawaby88/muid/actions/workflows/main.yml/badge.svg)
[![GitHub license](https://img.shields.io/github/license/rawaby88/muid)](LICENSE.md)

Laravel package to generate a random ID with your prefix for your Eloquent models

###Example

<p align="left">
    <img alt="Eloquent MUID" src="https://raw.githubusercontent.com/rawaby88/muid/main/media/muid-example.jpg" width="700px">
</p>


Available 3 lengths of Muid

| MUID | Char count |
| ------- | ------------ |
| `tiny` | `16 char` |
| `small` | `24 char` |
| `standard` | `36 char` |

the length can be altered from the config file if you wish.

| Laravel | Package |
| ------- | ------------ |
| `v8.*` | `v1.*` |
| `v9.*` | `v2.*` |
| `v10.*` | `v3.*` |




## Installation

You can install the package via Composer:

```bash
composer require rawaby88/muid
```

## Usage

You can extend the provided model classes, or by using a trait

### Extending model
When creating an Eloquent model, instead of extending the standard Laravel model class, 
extend from the model class provided by this package:

```php
namespace App\Models;

use Rawaby88\Muid\Database\Eloquent\Model;

class Organization extends Model
{
    /**
	 * The "prefix" of the MUID.
	 *
	 * @var string
	 */
	protected $keyPrefix = 'org_';
}
```
### Extending user model

Extending the User class provided by this package:

```php
<?php

namespace App\Models;

use \Rawaby88\Muid\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
	 * The "prefix" of the MUID.
	 *
	 * @var string
	 */
	protected $keyPrefix = 'user_';
}
```
### Using trait
As an alternative to extending the classes in the examples above, you also have
the ability to use the provided trait instead

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Rawaby88\Muid\Database\Eloquent\Muid;

class Organization extends Model
{
    use Muid;
    
    /**
	 * The "prefix" of the MUID.
	 *
	 * @var string
	 */
	protected $keyPrefix = 'organization_';
}
```

### prefix
in order to generate the prefix for your muid, you will need to provide this information in the model itself
by adding `$keyPrefix`, if no prefix provided muid will be generated without prefix
```php
/**
 * The "prefix" of the MUID.
 *
 * @var string
 */
protected $keyPrefix = 'example_';

```

### Creating models

In addition to the `make:model` artisan command, you will now have access to
`muid:make:model` which has all the functionality of the standard `make:model`
command (with the exception of not being able to create a pivot model):

```bash
php artisan muid:make:model Models/Organization --all
```

### Database migration
This package includes all types to generate your MUID in an easy way

lists of available Blueprints:

| Blueprint                 | Size | Description |
|---------------------------| ------- | ------------ |
| **standard**              |||
| **`primaryMuid`**         | `36` | Create a new muid column as the primary key(s) for the table |
| **`muid`**                | `36` | Create a new muid column on the table |
| **`foreignMuid`**         | `36` | Create a new muid column on the table with a foreign key constraint |
| `muidMorphs`              | `36` | Add the proper columns for a polymorphic table using MUIDs |
| `nullableMuidMorphs`      | `36` | Add nullable columns for a polymorphic table using MUIDs |
| **small**                 |||
| `primarySmallMuid`        | `24` | Create a new muid column as the primary key(s) for the table |
| `smallMuid`               | `24` | Create a new muid column on the table |
| `foreignSmallMuid`        | `24` | Create a new muid column on the table with a foreign key constraint |
| `muidSmallMorphs`         | `24` | Add the proper columns for a polymorphic table using MUIDs |
| `nullableSmallMuidMorphs` | `24` | Add nullable columns for a polymorphic table using MUIDs |
| **tiny**                  |||
| `primaryTinyMuid`         | `16` | Create a new muid column as the primary key(s) for the table |
| `tinyMuid`                | `16` | Create a new muid column on the table |
| `foreignTinyMuid`         | `16` | Create a new muid column on the table with a foreign key constraint |
| `muidTinyMorphs`          | `16` | Add the proper columns for a polymorphic table using MUIDs |
| `nullableTinyMuidMorphs`  | `16` | Add nullable columns for a polymorphic table using MUIDs |


### Migration example
```php
<?php

Schema::create( 'model_with_primaryMuid_test', function ( Blueprint $table ): void
{
    $table->primaryMuid( 'id' );
    $table->string( 'name' );
    $table->timestamps();
} );

Schema::create( 'model_with_muid_test', function ( Blueprint $table ): void
{
    $table->muid( 'id' )->primary();
    $table->string( 'name' );
    $table->timestamps();
} );

Schema::create( 'model_with_foreignMuid_test', function ( Blueprint $table ): void
{
    $table->muid( 'id' )->primary();
    $table->foreignMuid( 'model_with_muid_test_id' )->constrained( 'model_with_muid_test' );
    $table->timestamps();
} );

Schema::create( 'model_with_muidMorph_test', function ( Blueprint $table ): void
{
    $table->muid( 'id' )->primary();
    $table->muidMorphs( 'testable' );
    $table->timestamps();
} );

Schema::create( 'model_with_nullableMuidMorphs_test', function ( Blueprint $table ): void
{
    $table->muid( 'id' )->primary();
    $table->nullableMuidMorphs( 'testable' );
    $table->timestamps();
} );

Schema::create( 'model_without_muid_test', function ( Blueprint $table ): void
{
    $table->primaryMuid( 'id' );
    $table->timestamps();
} );
```

### Publish Config

Once done, publish the config to your config folder using:

```
php artisan vendor:publish --provider="Rawaby88\Muid\MuidServiceProvider"
```

## Configuration

```php
/*
|--------------------------------------------------------------------------
| Muid length
|--------------------------------------------------------------------------
|
| Here you can change the MUID length.
| remember that length include [prefix, timestamp(6)chars] and the rest will be random bits
| recommended to have minimum of 16 chars
|
*/
'tiny_muid_length'  => 16,
'small_muid_length' => 24,
'muid_length'       => 36,

/*
|--------------------------------------------------------------------------
| Random string strings
|--------------------------------------------------------------------------
|
| Recommended not to change
|
*/
'alfa_small'   => 'abcdefghilkmnopqrstuvwxyz',
'alfa_capital' => 'ABCDEFGHILKMNOPQRSTUVWXYZ',
'digits'       => '0123456789',

/*
|--------------------------------------------------------------------------
| Capital Char options
|--------------------------------------------------------------------------
|
| Set it to FALSE if you wish not to use capital letters in the generated MUID
|
*/
'allow_capital' => TRUE,

```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security-related issues, please email github@dreamod.pl instead of using the issue tracker.

## Credits

-   [Mahmoud Osman](https://github.com/rawaby88)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
