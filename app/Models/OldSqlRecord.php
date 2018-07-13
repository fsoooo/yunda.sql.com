<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldSqlRecord extends Model{

	protected $connection = 'mysql';
    protected $table = 'sql_record';
}