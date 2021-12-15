<?php
namespace Byross\BOCPayment\Classes;

use Byross\BOCPayment\Exceptions\BOCMaintainingException;
use Byross\BOCPayment\Exceptions\BOCKeyPairException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock\Tags\See;

class BOCPay
{
    use HasFactory;

    protected $client;

    protected $base_url;
    protected $transaction_url;
    protected $statement_url;
    protected $platform_public_key;
    protected $server_private_key;


    protected $env;

    public $log_channel = null;

    protected $base_fields = [
        'requestId' => null,
        'service' => null,
        'version' => '2.0',
        'signType' => 'RSA2',
        'merchantId' => null,
        'merchantSign' => null,
        'terminalNo' => null,
    ];

    public function __construct($terminal_type = 'online'){
        $config = config('boc-macau-payment');
        $this->env = $config['production']? 'prod': 'uat';
        $this->log_channel = $config['log_channel'];

        $this->base_url = $config[ $this->env ]['base_url'];
        $this->transaction_url = $config[ $this->env ]['transaction_url'];
        $this->statement_url = $config[ $this->env ]['statement_url'];
        $this->platform_public_key = $config[ $this->env ]['platform_public_key'];
        $this->server_private_key = $config[ $this->env ]['server_private_key'];

        $fields = [
            'merchantId' => $config[ $this->env ]['merchant_id'],
            'terminalNo' => $config[ $this->env ]['terminal_no'][$terminal_type],
        ];
        $this->base_fields = array_merge($this->base_fields, $fields);

        $this->client = new Client([
            'base_uri' =>  $config[ $this->env ]['base_url'],
            'timeout' =>  6.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => ''
            ]
        ]);

//        $this->client = Http::withHeaders([
//            'Content-Type' => 'application/json',
//            'Accept' => 'application/json',
//            'User-Agent' => ''
//        ]);

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

        $this->log('info', $data_string);

        $private_key_path = $this->server_private_key;
        $private_key = openssl_get_privatekey(file_get_contents($private_key_path));
        openssl_sign($data_string, $signature, $private_key, OPENSSL_ALGO_SHA256);
        openssl_free_key($private_key);

        $data['merchantSign'] = base64_encode($signature);
        $this->log('info', $data['merchantSign']);

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
        $this->log('info', $data_string);

        $pub_key_path = $this->platform_public_key;
        $public_key = openssl_get_publickey(file_get_contents($pub_key_path));
        $verified = openssl_verify($data_string, $signature, $public_key, OPENSSL_ALGO_SHA256);
        openssl_free_key($public_key);

        $this->log('info', $data['serverSign']);
        if (!$verified){
            $this->log('error', 'Invalid Server Signature');
            throw new BOCKeyPairException('Invalid Server Signature');
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
            'validNumber' => null,
            'requestId' => time() . Str::padLeft(rand(1, 99999), 5, '0')
        ];
        $data = array_merge($this->base_fields, $data, $input_array);

        $data = $this->withSignature($data);

        $this->log('info', $data);

        $response = $this->client->post($this->transaction_url, [
            'json' => $data
        ]);

        $this->log('info', $response->getBody());

        $body = json_decode(urldecode($response->getBody()), true);

        $this->checkExceptions($body);

        if (!isset($body['result'])){
            return ['error' => 1, 'error_msg' => $body['returnMessage']];
        }

        if ($verify_boc_sign){
            $this->verifySignature($body);
        }

        $body = Arr::except($body, ['version', 'signType', 'serverSign']);

        return $body;
    }

    public function orderQuery($input_array, $verify_boc_sign = true){
        $data = [
            'service' => 'OrderQuery',
            'queryLogNo' => null,
            'requestId' => time() . Str::padLeft(rand(1, 99999), 5, '0')
        ];
        $data = array_merge($this->base_fields, $data, $input_array);
        $data = $this->withSignature($data);

        $this->log('info', $data);

        $response = $this->client->post($this->transaction_url, [
            'json' => $data
        ]);

        $this->log('info', $response->getBody());

        $body = json_decode(urldecode($response->getBody()), true);

        $this->checkExceptions($body);

        if (!isset($body['result'])){
            return ['error' => 1, $body['returnMessage']];
        }

        if ($verify_boc_sign){
            $this->verifySignature($body);
        }

        $body = Arr::except($body, ['version', 'signType', 'serverSign']);

        return $body;
    }

    public function orderRefund($input_array, $verify_boc_sign = true){
        $data = [
            'service' => 'OrderRefund',
            'requestId' => time() . Str::padLeft(rand(1, 99999), 5, '0')
        ];
        $data = array_merge($this->base_fields, $data, $input_array);
        $data = $this->withSignature($data);

        $this->log('info', $data);

        $response = $this->client->post($this->transaction_url, $data);

        $this->log('info', $response->getBody());

        $body = json_decode(urldecode($response->getBody()), true);

        $this->checkExceptions($body);

        if (!isset($body['result'])){
            return ['error' => 1, $body['returnMessage']];
        }

        if ($verify_boc_sign){
            $this->verifySignature($body);
        }

        $body = Arr::except($body, ['version', 'signType', 'serverSign']);

        return $body;
    }

    public function log($level, $message){
        if ($this->log_channel){
            Log::channel($this->log_channel)->$level($message);
        }
    }


    public function getOrderQueryNewRequest($input_array, $verify_boc_sign = true){
//        $client = new Client([
//            'base_uri' =>  $this->base_url,
//            'timeout' =>  6.0,
//            'headers' => [
//                'Content-Type' => 'application/json',
//                'Accept' => 'application/json',
//                'User-Agent' => ''
//            ]
//        ]);
        $data = [
            'service' => 'OrderQuery',
            'queryLogNo' => null,
            'requestId' => time() . Str::padLeft(rand(1, 99999), 5, '0')
        ];

        $data = array_merge($this->base_fields, $data, $input_array);
        $data = $this->withSignature($data);

        $request = new Request('POST', Str::substr($this->base_url, 0, -1) .  $this->transaction_url,
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => ''
            ],
            json_encode($data)
        );

        return $request;

    }


}
