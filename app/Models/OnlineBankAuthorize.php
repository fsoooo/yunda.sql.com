<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineBankAuthorize extends Model{

	protected $connection = 'online_mysql';
    protected $table = 'bank_authorize';
}