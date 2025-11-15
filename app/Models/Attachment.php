<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'file_path',
        'file_type',
        'complaint_id'
    ];
    
    public function complaint() {
        return $this->belongsTo(Complaint::class);
    }

}
