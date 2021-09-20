<?php

namespace Byross\BOCPayment\Exceptions;

use Exception;
use Throwable;

class BOCKeyPairException extends Exception
{
    //
    public $message;
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = $message;

    }

    public function report(){
        return ['error' => 20, 'error_msg' => $this->message];
    }

}
