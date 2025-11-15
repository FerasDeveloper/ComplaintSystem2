<?php

namespace App\Repositories;


use App\Models\Complaint;
use Illuminate\Http\UploadedFile;

interface ComplaintRepositoryInterface
{
  public function create(array $data): Complaint;
  public function addAttachment(string $path,string $extension, int $complaint_id): Complaint;
  public function find(int $id): Complaint;
  public function getComplaints();
}
