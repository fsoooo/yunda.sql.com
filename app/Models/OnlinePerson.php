<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlinePerson extends Model{

	protected $connection = 'online_mysql';
    protected $table = 'person';
}