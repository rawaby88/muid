<?php

namespace Rawaby88\Muid\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\DB;
use Rawaby88\Muid\Exceptions\KeyLengthException;
use Rawaby88\Muid\MuidService;

class Model extends BaseModel
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "prefix" of the MUID.
     *
     * @var string
     */
    protected $keyPrefix = 'usr_';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the IDs are MUIDs.
     *
     * @var bool
     */
    protected $keyIsMuid = true;

    /**
     * Indicates if the IDs are MUIDs.
     *
     * @var int
     */
    protected $keyLength;

    /**
     * The "booting" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Automatically generate a MUID if using them, and not provided.
        static::creating(function (self $model): void {
            DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            if ($model->keyIsMuid) {
                $con = DB::connection();
                $sm = $con->getDoctrineSchemaManager();
                $table = $sm->listTableDetails($model->getTable());
                $pk = $table->getPrimaryKey()
                    ->getColumns()[0];
                if (empty($model->{$pk})) {
                    $length = $con->getDoctrineColumn($model->getTable(), $pk)->getLength() ?? $model->keyLength;

                    if (! $length) {
                        throw new KeyLengthException();
                    }

                    $model->{$pk} = (new MuidService($length, $model->keyPrefix))->generate();
                }
            }
        });
    }
}
