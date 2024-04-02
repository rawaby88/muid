<?php

namespace Rawaby88\Muid\Tests\Models;

use Rawaby88\Muid\Database\Eloquent\Model;

/**
 * @method static create( string[] $array )
 * @method static count()
 */
class ModelWithoutMuidExtendsTest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_without_muid_test';

    protected $keyPrefix = 'without_';

    protected $keyLength = 36;

    protected $keyIsMuid = false;
}
