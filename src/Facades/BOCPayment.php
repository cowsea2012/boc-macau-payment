<?php

namespace Byross\BOCPayment\Facades;

use Illuminate\Support\Facades\Facade;
use phpDocumentor\Reflection\Types\Boolean;


/**
 * @method static Boolean verifySignature(array $data = null)
 */

class BOCPayment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public const ORDER_SUCCESS = 'S';
    public const ORDER_FAILED = 'F';
    public const ORDER_PENDING = 'A';
    public const ORDER_UNKNOWN = 'Z';
    public const ORDER_REFUNDING = 'W';
    public const ORDER_REVOKED = 'D';

    protected static function getFacadeAccessor()
    {
        return 'boc-macau-payment';
    }
}
