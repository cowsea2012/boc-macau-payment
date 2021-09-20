<?php
namespace Byross\BOCPayment\Classes;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Http;

class BOCClient extends Http
{
    use HasFactory;

    public $transaction_url;
    public $statement_url;
    public $platform_public_key;
    public $server_private_key;

    protected $env;

    public $base_fields = [
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

//        $this->config
//        $this->client = Http::withHeaders([
//            'Content-Type' => 'application/json',
//            'Accept' => 'application/json'
//        ]);


        self::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);
//        parent::__construct($config);
    }

    public function getTransactionUrl(){
        return $this->transaction_url;
    }
    public function getStatementUrl(){
        return $this->statement_url;
    }



}
