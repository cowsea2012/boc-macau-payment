<?php
namespace Byross\BOCPayment\Classes;

use Byross\BOCPayment\Exceptions\BOCMaintainingException;
use Byross\BOCPayment\Exceptions\BOCKeyPairException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\DocBlock\Tags\See;

class BOCPay
{
    use HasFactory;

    protected $client;
    protected $transaction_url;
    protected $statement_url;
    protected $platform_public_key;
    protected $server_private_key;

    protected $env;

    protected $base_fields = [
        'requestId' => null,
        'service' => null,
        'version' => '2.0',
        'signType' => 'RSA2',
        'merchantId' => null,
        'merchantSign' => null,
        'terminalNo' => null,
    ];

    public function __construct(){
        $config = config('boc-macau-payment');
        $this->env = $config['production']? 'prod': 'uat';

        $this->transaction_url = $config[ $this->env ]['transaction_url'];
        $this->statement_url = $config[ $this->env ]['statement_url'];
        $this->platform_public_key = $config[ $this->env ]['platform_public_key'];
        $this->server_private_key = $config[ $this->env ]['server_private_key'];

        $fields = [
            'merchantId' => $config[ $this->env ]['merchant_id'],
            'terminalNo' => $config[ $this->env ]['terminal_no'],
        ];
        $this->base_fields = array_merge($this->base_fields, $fields);

        $this->client = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);

    }

    public function checkExceptions($body){
        if (!$body){
            throw new BOCKeyPairException();
        }

        if ($body['returnCode'] == '888888'){
            throw new BOCMaintainingException($body['returnMessage']);
        }
    }
    public function withSignature($data){
        ksort($data);
        $data_string = '';
        foreach ($data as $key => $value){
            if (!is_null($value) && $key != 'merchantSign'){
                $data_string .= $value;
            }
        }
        dump($data_string);
        $private_key_path = $this->server_private_key;
        $private_key = openssl_get_privatekey(file_get_contents($private_key_path));
        openssl_sign($data_string, $signature, $private_key, OPENSSL_ALGO_SHA256);
        openssl_free_key($private_key);

        $data['merchantSign'] = base64_encode($signature);
        dump($data['merchantSign']);
        foreach ($data as $key => $value){
            if ($value && !empty($value)){
                $data[$key] = urlencode($value);
            }
        }

        return $data;
    }

    public function verifySignature($data){
        $signature = base64_decode($data['serverSign']);

        ksort($data);

        $data_string = '';
        foreach ($data as $key => $value){
            if (!is_null($value) && $key != 'serverSign'){
                $data_string .= urldecode($value);
            }
        }

        dump($data_string);
        dump($data['serverSign']);
        $pub_key_path = $this->platform_public_key;
        $public_key = openssl_get_publickey(file_get_contents($pub_key_path));
        $verified = openssl_verify($data_string, $signature, $public_key, OPENSSL_ALGO_SHA256);
        openssl_free_key($public_key);

        if (!$verified){
            dump('fail');
//            throw new BOCKeyPairException('Public Key Error');

        }

        return $verified;
    }

    public function createBocCashier($input_array, $verify_boc_sign = true){
        $data = [
            'service' => 'CreateBocCashier',
            'cashierLanguage' => 'zh_TW',
            'payChannel' => 'ALL',
            'productCode' => 'PCWEB',
            'notifyUrl' => 'api',
            'referUrl' => 'api',
            'businessType' => '4',
            'validNumber' => null
        ];
        $data = array_merge($this->base_fields, $data, $input_array);

        $data = $this->withSignature($data);

        $response = $this->client->post($this->transaction_url, $data);

//        Log::info($response->getBody());
        $body = json_decode(urldecode($response->getBody()), true);

        $this->checkExceptions($body);

        if (!isset($body['result'])){
            return ['error' => 1, 'error_msg' => $body['returnMessage']];
        }

        if ($verify_boc_sign){
            $this->verifySignature($body);
        }


//        $body = Arr::except($body, ['version', 'signType', 'serverSign']);

        return $body;
    }

    public function orderQuery($input_array, $verify_boc_sign = true){
        $data = [
            'service' => 'OrderQuery',
            'queryLogNo' => '',
        ];
        $data = array_merge($this->base_fields, $data, $input_array);
        $data = $this->withSignature($data);

//        Log::info($data);
        dump($data);
        $response = $this->client->post($this->transaction_url, $data);
//        Log::error($response->getBody());
        $body = json_decode(urldecode($response->getBody()), true);
        dump($body);
        $this->checkExceptions($body);

        if (!isset($body['result'])){
            return ['error' => 1, $body['returnMessage']];
        }

        if ($verify_boc_sign){
            $this->verifySignature($body);
        }

//        $body = Arr::except($body, ['version', 'signType', 'serverSign']);

        return $body;
    }
    public function orderRefund($input_array, $verify_boc_sign = true){
        $data = [
            'service' => 'OrderRefund',
            'logNo' => '',
            'refundOrderNo' => '',
            'refundAmount' => null,
        ];
        $data = array_merge($this->base_fields, $data, $input_array);
        $data = $this->withSignature($data);
        dump($data);
        $response = $this->client->post($this->transaction_url, $data);
        $body = json_decode(urldecode($response->getBody()), true);
        dump($body);
        $this->checkExceptions($body);

        if (!isset($body['result'])){
            return ['error' => 1, $body['returnMessage']];
        }

        if ($verify_boc_sign){
            $this->verifySignature($body);
        }
        return $body;
    }
}
