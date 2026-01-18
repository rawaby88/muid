<?php

namespace Rawaby88\Muid\Database\Eloquent;

use Doctrine\DBAL\Exception;
use Rawaby88\Muid\Exceptions\KeyLengthException;
use Rawaby88\Muid\MuidService;

trait Muid
{
    /**
     * Indicates if the IDs are MUIDs.
     *
     * @var bool
     */
    protected bool $keyIsMuid = true;

    /**
     * The "booting" method of the model.
     *
     * @throws KeyLengthException
     * @throws Exception
     */
    protected static function booting(): void
    {
        // Automatically generate a MUID if using them, and not provided.
        static::creating(function (self $model): void {

            if ($model->keyIsMuid) {
                $pk = $model->getKeyName();
                if (empty($model->{$pk})) {
                    $length =  $model->keyLength;
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
