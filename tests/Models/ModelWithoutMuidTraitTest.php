<?php

namespace Rawaby88\Muid\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Rawaby88\Muid\Database\Eloquent\Muid;

/**
 * @method static create( string[] $array )
 * @method static count()
 */
class ModelWithoutMuidTraitTest extends Model
{
    use Muid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_without_muid_test';

    protected $keyPrefix = 'without_';

    protected $keyLength = 36;

    /**
     * Indicates if the IDs are MUIDs.
     */
    protected function keyIsMuid(): bool
    {
        return false;
    }
}
