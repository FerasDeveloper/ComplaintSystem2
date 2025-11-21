<?php

namespace App\Services;

use App\Jobs\StoreAttachmentJob;
use App\Repositories\ComplaintRepositoryInterface;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;

class ComplaintServices
{
  protected $complaints;

  public function __construct(ComplaintRepositoryInterface $complaints)
  {
    $this->complaints = $complaints;
  }

  public function addComplaint($user, $data, $attachments): void
  {
    $data['user_id'] = $user->id;
    $data['government_id'] = is_numeric($data['government_id'])
      ? (int) $data['government_id']
      : $data['government_id'];

    $complaint = $this->complaints->create($data);

    if (!empty($attachments)) {
      foreach ($attachments as $file) {
        $path = $file->store('attachments', 'public');
        $extension = $file->getClientOriginalExtension();
        StoreAttachmentJob::dispatch($path, $extension, $complaint->id);
      }
    } else {
      $complaint->update(['status' => 'new']);
    }
  }

  public function showComplaint($id): Complaint
  {
    return $this->complaints->find($id);
  }

  public function getComplaints()
  {
    return $this->complaints->getComplaints();
  }

  public function editComplaint($id, $user, $data)
  {
    if ($user->role_id != 2 && $user->role_id != 3) {
      return response()->json([
        'success' => false,
        'message' => 'Unauthorized to edit complaint'
      ], 403);
    }

    return DB::transaction(function () use ($id, $user, $data) {
      $complaint = $this->complaints->find($id);

      if (!empty($complaint->editing_by) && $complaint->editing_by !== $user->name) {
        return response()->json([
          'success' => false,
          'message' => "Cannot edit this complaint as it is being edited by $complaint->editing_by"
        ], 423);
      }

      $complaint = Complaint::lockForUpdate()->find($id);

      $complaint->editing_by = $user->name;
      $complaint->save();

      $userInfo['role'] = $user->role_id == 2 ? 'government' : 'employee';
      $userInfo['user_id'] = $user->id;

      $this->complaints->update($complaint, $data);
      $this->complaints->addComplaintLogs($complaint,$userInfo, $data);

      $complaint->editing_by = null;
      $complaint->save();

      return response()->json([
        'success' => true,
        'message' => 'Complaint updated successfully'
      ]);
    });
  }
  public function getComplaintLog(int $id) {
    return $this->complaints->getComplaintLog($id);
  }
  
}
