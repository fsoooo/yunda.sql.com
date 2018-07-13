<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlinePerson extends Model{

	protected $connection = 'online_mysql';
    protected $table = 'person';

    public function personRefer(){
		return $this->hasOne('App\Models\OnlinePersonRefer','person_id', 'id');
	}
}