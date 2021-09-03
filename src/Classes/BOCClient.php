<?php
namespace Byross\BOCPayment\Classes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Http;

class BOCClient
{
    use HasFactory;

    protected $transaction_url;
    protected $statement_url;

    public $client;

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

        $this->transaction_url = $config['url'][ $this->env ]['transaction'];
        $this->statement_url = $config['url'][ $this->env ]['statement'];

        $fields = [
            'merchantId' => $config['fields'][ $this->env ]['merchant_id'],
            'terminalNo' => $config['fields'][ $this->env ]['terminal_no'],
        ];
        $this->base_fields = array_merge($this->base_fields, $fields);

        $this->client = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);
    }

    public function getTransactionUrl(){
        return $this->transaction_url;
    }
    public function getStatementUrl(){
        return $this->statement_url;
    }

    public function withSignature($data){
        ksort($data);
        $data_string = '';
        foreach ($data as $key => $value){
            if ($value && !empty($value) && $key != 'merchantSign'){
                $data_string .= $value;
            }
        }
        $private_key = openssl_get_privatekey(file_get_contents(base_path() . '/bochook.key'));
        openssl_sign($data_string, $signature, $private_key, OPENSSL_ALGO_SHA256);
        openssl_free_key($private_key);

        $data['merchantSign'] = base64_encode($signature);
        foreach ($data as $key => $value){
            if ($value && !empty($value)){
                $data[$key] = urlencode($value);
            }
        }

        return $data;
    }

}
