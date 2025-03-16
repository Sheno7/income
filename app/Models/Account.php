<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function balance(){
        return $this->hasMany(Balance::class,'account_id','id');
    }
}
