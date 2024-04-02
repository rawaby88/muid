<?php

namespace Rawaby88\Muid\Exceptions;

use Exception;

class KeyLengthException extends Exception
{
    /**
     * KeyLengthException constructor.
     */
    public function __construct()
    {
        parent::__construct("Your database doesn't support key length please add MUID variable \$keylength to the model");
    }
}
