<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'production' => false,
    'log_channel' => false,
    'uat' => [
        'transaction_url' => 'https://cuaas.bocmacau.com/w/rsa/mercapi_ol',
        'statement_url' => 'https://cuaam.bocmacau.com/mercweb/rsa/api_ol',
        'merchant_id' => '',
        'terminal_no' => [
            'offline' => '',
            'online' => ''
        ],
        'platform_public_key' => base_path() . 'platform_public.key',
        'server_private_key' => base_path() . 'server_private.key'
    ],
    'prod' => [
        'transaction_url' => 'https://aas.bocmacau.com/w/rsa/mercapi_ol',
        'statement_url' => 'https://aam.bocmacau.com/mercweb/rsa/api_ol',
        'merchant_id' => '',
        'terminal_no' => [
            'offline' => '',
            'online' => ''
        ],
        'platform_public_key' => base_path() . 'platform_public.key',
        'server_private_key' => base_path() . 'server_private.key'
    ],


];
