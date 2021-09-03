<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'production' => false,
    'url' => [
        'uat' => [
            'transaction' => 'https://cuaas.bocmacau.com/w/rsa/mercapi_ol',
            'statement' => 'https://cuaam.bocmacau.com/mercweb/rsa/api_ol'
        ],
        'prod' => [
            'transaction' => 'https://aas.bocmacau.com/w/rsa/mercapi_ol',
            'statement' => 'https://aam.bocmacau.com/mercweb/rsa/api_ol'
        ],
    ],
    'fields' => [
        'uat' => [
            'merchant_id' => '',
            'terminal_no' => ''
        ],
        'prod' => [
            'merchant_id' => '',
            'terminal_no' => ''
        ],
    ],


];
