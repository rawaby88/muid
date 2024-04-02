<?php

namespace Rawaby88\Muid\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Rawaby88\Muid\Database\Eloquent\Muid;

/**
 * @method static create( string[] $array )
 */
class ModelWithPrimaryMuidTraitTest extends Model
{
    use Muid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_with_primaryMuid_test';

    protected $keyPrefix = 'primary_';

    protected $keyLength = 36;
}
