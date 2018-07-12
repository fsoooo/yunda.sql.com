<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model{
	protected $connection = 'mysql';
    protected $table = 'person';

}