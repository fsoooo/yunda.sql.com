<?php
namespace App\Api\Controllers;


use App\Api\Transformers\TestsTransformer;
use App\Models\Account;

class TestsController extends BaseController
{
    public function index()
    {
        $tests = Account::all();
        return $this->collection($tests, new TestsTransformer());
    }

    public function show($id)
    {
        $test = Account::find($id);
        if (!$test) {
            return $this->response->errorNotFound('Test not found');
        }
        return $this->item($test, new TestsTransformer());
    }
}