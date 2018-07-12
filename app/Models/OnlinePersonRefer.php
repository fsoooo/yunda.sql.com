<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlinePersonRefer extends Model{

	protected $connection = 'online_mysql';
    protected $table = 'person_refer';
}