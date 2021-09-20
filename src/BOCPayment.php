<?php

namespace Byross\BOCPayment;

use Byross\BOCPayment\Classes\BOCPay;
use Illuminate\Support\Arr;

class BOCPayment
{
    public $boc;
    // Build your next great package.
    public function __construct(){
        $this->boc = new BOCPay();
    }

    public function postCreateBocCashier($input_array){
        return $this->boc->createBocCashier($input_array);
    }

    public function postOrderQuery($input_array){
        return $this->boc->orderQuery($input_array);
    }

    public function postOrderRefund($input_array){
        return $this->boc->orderRefund($input_array);
    }

}
