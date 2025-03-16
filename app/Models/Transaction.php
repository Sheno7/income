<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function source(){
        return $this->belongsTo(Source::class);
    }
}
