<?php

namespace Rawaby88\Muid\Database\Eloquent;

use Doctrine\DBAL\Exception;
use Illuminate\Support\Facades\DB;
use Rawaby88\Muid\Exceptions\KeyLengthException;
use Rawaby88\Muid\MuidService;

trait Muid
{
    /**
     * Indicates if the IDs are MUIDs.
     *
     * @var bool
     */
    protected $keyIsMuid = true;

    /**
     * The "booting" method of the model.
     *
     * @throws KeyLengthException
     * @throws Exception
     */
    protected static function bootMuid(): void
    {
        // Automatically generate a MUID if using them, and not provided.
        static::creating(function (self $model): void {
            DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

            if ($model->keyIsMuid) {
                $con = DB::connection();
                $sm = $con->getDoctrineSchemaManager();
                $table = $sm->listTableDetails($model->getTable());
                $pk = $table->getPrimaryKey()->getColumns()[0];

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

    public function initializeMuid()
    {
        $this->incrementing = false;
        $this->keyType = 'string';
        $this->guarded = [];
    }
}
