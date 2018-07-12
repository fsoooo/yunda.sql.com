<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SqlRecord extends Model{

	protected $connection = 'mysql';
    protected $table = 'sql_record';
}