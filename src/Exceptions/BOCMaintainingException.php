<?php

namespace Byross\BOCPayment\Exceptions;

use Exception;
use Throwable;

class BOCMaintainingException extends Exception
{
    //
    public $maintain_message;
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->maintain_message = $message;

    }

    public function report(){
        return ['error' => 888888, 'error_msg' => $this->maintain_message];
    }

}
