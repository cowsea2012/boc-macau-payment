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

    public function verifySignature($data){
        return $this->boc->verifySignature($data);
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

    public function getOrderQueryNewRequest($input_array, $verify_boc_sign = true){
        return $this->boc->getOrderQueryNewRequest($input_array, $verify_boc_sign);
    }

}
