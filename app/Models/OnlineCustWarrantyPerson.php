<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineCustWarrantyPerson extends Model{

	protected $connection = 'online_mysql';
    protected $table = 'cust_warranty_person';
}