<?php

namespace Rawaby88\Muid\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rawaby88\Muid\Database\Eloquent\Muid;

/**
 * @method static create( string[] $array )
 */
class ModelWithForeignMuidTraitTest extends Model
{
    use Muid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'model_with_foreignMuid_test';

    protected $keyPrefix = 'foreign_';

    protected $keyLength = 36;

    public function belongsToModelWithMuidTest(): BelongsTo
    {
        return $this->belongsTo(ModelWithMuidExtendsTest::class);
    }
}
