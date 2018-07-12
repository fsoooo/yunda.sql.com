<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineAccount extends Model{

	protected $connection = 'online_mysql';
    protected $table = 'account';
}