<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    //
	public $table = 'p_users';
//	public $timestamps = true;
//	public $updated_at=false;
    protected $primaryKey = 'uid';
}