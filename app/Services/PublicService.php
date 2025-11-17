<?php

namespace App\Services;

use App\Models\ComplaintType;
use App\Models\Government;

class PublicService
{
  public function getGovernments()
  {
    return Government::all(['id', 'name']);
  }

  public function getComplaintTypes()
  {
    return ComplaintType::all(['id', 'name']);
  }
}
