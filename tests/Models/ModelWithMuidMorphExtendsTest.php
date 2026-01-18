<?php

namespace Rawaby88\Muid\Tests\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rawaby88\Muid\Database\Eloquent\Model;

/**
 * @method static create( string[] $array )
 */
class ModelWithMuidMorphExtendsTest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_with_muidMorph_test';

    protected $keyPrefix = 'morph_';

    protected $keyLength = 36;

    public function testable(): MorphTo
    {
        return $this->morphTo();
    }
}
