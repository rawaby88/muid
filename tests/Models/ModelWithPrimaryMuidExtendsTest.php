<?php

namespace Rawaby88\Muid\Tests\Models;

use Rawaby88\Muid\Database\Eloquent\Model;

/**
 * @method static create( string[] $array )
 */
class ModelWithPrimaryMuidExtendsTest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_with_primaryMuid_test';

    protected $keyPrefix = 'primary_';

    protected $keyLength = 36;
}
