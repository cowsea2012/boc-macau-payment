<?php

namespace Byross\BOCPayment;

use Byross\BOCPayment\Classes\BOCPay;
use Illuminate\Support\Arr;

class BOCPayment
{
    public $boc;

    const ORDER_SUCCESS = 'S';
    const ORDER_FAILED = 'F';
    const ORDER_PENDING = 'A';
    const ORDER_UNKNOWN = 'Z';
    const ORDER_REFUNDING = 'W';
    const ORDER_REVOKED = 'D';
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
