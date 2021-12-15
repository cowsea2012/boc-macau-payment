<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'production' => false,
    'log_channel' => false,
    'uat' => [
        'base_url' => 'https://cuaas.bocmacau.com/',
        'transaction_url' => '/w/rsa/mercapi_ol',
        'statement_url' => '/mercweb/rsa/api_ol',
        'merchant_id' => '',
        'terminal_no' => [
            'offline' => '',
            'online' => ''
        ],
        'platform_public_key' => base_path() . 'platform_public.key',
        'server_private_key' => base_path() . 'server_private.key'
    ],
    'prod' => [
        'base_url' => 'https://aas.bocmacau.com/',
        'transaction_url' => '/w/rsa/mercapi_ol',
        'statement_url' => '/mercweb/rsa/api_ol',
        'merchant_id' => '',
        'terminal_no' => [
            'offline' => '',
            'online' => ''
        ],
        'platform_public_key' => base_path() . 'platform_public.key',
        'server_private_key' => base_path() . 'server_private.key'
    ],


];
