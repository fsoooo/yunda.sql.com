<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldPerson extends Model{
	protected $connection = 'mysql';
    protected $table = 'person';

}