<?php

return [
    'client_id'       => env('DOITPAY_CLIENT_ID', ''),
    'secret_key'      => env('DOITPAY_SECRET_KEY', ''),
    'merchant_id'     => env('DOITPAY_MERCHANT_ID', ''),
    'sub_merchant_id' => env('DOITPAY_SUB_MERCHANT_ID', ''),
    'nmid'            => env('DOITPAY_NMID', ''),
    'channel_id'      => env('DOITPAY_CHANNEL_ID', 'BR'),
    'base_url'        => env('DOITPAY_BASE_URL', 'https://sandbox.doitpay.co'),
    'private_key_path' => env('DOITPAY_PRIVATE_KEY_PATH', ''),
    'is_production'   => env('DOITPAY_IS_PRODUCTION', false),
    'callback_url'    => env('DOITPAY_CALLBACK_URL', ''),
    'source_account'  => env('DOITPAY_SOURCE_ACCOUNT', ''),
    'issuer_name'     => env('DOITPAY_ISSUER_NAME', 'nobu'),
    'merchant_pan'    => env('DOITPAY_MERCHANT_PAN', ''),
];
