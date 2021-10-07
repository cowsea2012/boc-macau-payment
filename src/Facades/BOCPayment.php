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

    protected static function getFacadeAccessor()
    {
        return 'boc-macau-payment';
    }
}
