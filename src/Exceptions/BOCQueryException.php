<?php

namespace Byross\BOCPayment\Exceptions;

use Exception;

class BOCQueryException extends Exception
{
    //
    public function report(){
        return ['error' => 1, 'error_msg' => 'Query error'];
    }

}
