<?php

namespace App\Repositories;


use App\Models\Complaint;

interface ComplaintRepositoryInterface
{
  public function create(array $data): Complaint;
  public function addAttachment(string $path, string $extension, int $complaint_id): Complaint;
  public function find(int $id): Complaint;
  public function update(Complaint $complaint, array $data): void;
  public function getComplaints();
  public function addComplaintLogs(Complaint $complaint,array $userInfo, array $data): void;
}
