<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
  protected $fillable = [
    'title',
    'location',
    'description',
    'type_id',
    'status',
    'editing_by',
    'user_id',
    'government_id',
    'attachments_count',
    'processed_attachments'
  ];

  public function citizen()
  {
    return $this->belongsTo(User::class);
  }

  public function government()
  {
    return $this->belongsTo(Government::class);
  }

  public function logs()
  {
    return $this->hasMany(ComplaintLog::class);
  }

  public function attachments()
  {
    return $this->hasMany(Attachment::class);
  }
}
