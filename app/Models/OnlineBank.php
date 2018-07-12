<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineBank extends Model{

	protected $connection = 'online_mysql';
    protected $table = 'bank';
}