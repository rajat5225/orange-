<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $table='user_documents';
    
    public function docType()
    {
    	return $this->belongsTo('App\Model\DocumentType', 'document_type_id', 'id');
    }
}
