<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'rest-api/*',
        'channelsapi/*',
        '/insurance/order_status',
        '/ins/call_back',
        '/ins/quote',
        '/backend/set_brokerage/set_brokerage_post',
        '/backend/setting/*',
        '/backend/ajax/uploadImage'
    ];
}
