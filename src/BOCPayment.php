<?php

namespace Byross\BOCPayment;

use Byross\BOCPayment\Classes\BOCPay;
use Illuminate\Support\Arr;

class BOCPayment
{
    public $boc;

    public const ORDER_SUCCESS = 'S';
    public const ORDER_FAILED = 'F';
    public const ORDER_PENDING = 'A';
    public const ORDER_UNKNOWN = 'Z';
    public const ORDER_REFUNDING = 'W';
    public const ORDER_REVOKED = 'D';

    // Build your next great package.
    public function __construct(){
        $this->boc = new BOCPay();
    }

    public function postCreateBocCashier($input_array, $verify_boc_sign = true){
        return $this->boc->createBocCashier($input_array, $verify_boc_sign);
    }

    public function postOrderQuery($input_array, $verify_boc_sign = true){
        return $this->boc->orderQuery($input_array, $verify_boc_sign);
    }

    public function postOrderRefund($input_array, $verify_boc_sign = true){
        return $this->boc->orderRefund($input_array, $verify_boc_sign);
    }

}
