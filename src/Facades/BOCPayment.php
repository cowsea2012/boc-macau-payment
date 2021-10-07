<?php

namespace Byross\BOCPayment\Facades;

use Illuminate\Support\Facades\Facade;


class BOCPayment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    const ORDER_SUCCESS = 'S';
    const ORDER_FAILED = 'F';
    const ORDER_PENDING = 'A';
    const ORDER_UNKNOWN = 'Z';
    const ORDER_REFUNDING = 'W';
    const ORDER_REVOKED = 'D';

    protected static function getFacadeAccessor()
    {
        return 'boc-macau-payment';
    }
}
