<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TrustedContact extends Model
{
    protected $table='trusted_contacts';
    
    public function parent()
    {
    	return $this->belongsTo('App\Model\User', 'parent_id', 'id');
    }

    public function user()
    {
    	return $this->belongsTo('App\Model\User');
    }
}
