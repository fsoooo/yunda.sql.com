<?php

namespace App\Api\Controllers;

use App\Http\Controllers\PersonController;
use Dingo\Api\Routing\Helpers;


class BasePersonController extends PersonController
{
    use Helpers;

    /****
     * BasePersonController constructor.
     */
    public function __construct()
    {

    }
}