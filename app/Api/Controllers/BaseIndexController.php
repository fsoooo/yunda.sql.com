<?php

namespace App\Api\Controllers;

use App\Http\Controllers\IndexController;
use Dingo\Api\Routing\Helpers;


class BaseIndexController extends IndexController
{
    use Helpers;

    /****
     * BaseIndexController constructor.
     */
    public function __construct()
    {

    }
}