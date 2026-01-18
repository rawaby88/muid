<?php

namespace Rawaby88\Muid\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rawaby88\Muid\Database\Eloquent\Muid;

/**
 * @method static create( string[] $array )
 */
class ModelWithMuidMorphTraitTest extends Model
{
    use Muid;

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
