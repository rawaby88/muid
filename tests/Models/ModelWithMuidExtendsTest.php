<?php

namespace Rawaby88\Muid\Tests\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rawaby88\Muid\Database\Eloquent\Model;

/**
 * @method static create( string[] $array )
 * @method static count()
 */
class ModelWithMuidExtendsTest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_with_muid_test';

    protected $keyPrefix = 'test_';

    protected $keyLength = 36;

    public function modelWithMuidMorph(): MorphMany
    {
        return $this->morphMany(ModelWithMuidMorphExtendsTest::class, 'testable');
    }
}
