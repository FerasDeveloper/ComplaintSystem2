<?php

namespace App\Services;

use App\Aspects\MethodAspect;
use App\Aspects\TransactionAspect;
use App\Jobs\StoreAttachmentJob;
use App\Repositories\ComplaintRepositoryInterface;
use App\Models\Complaint;

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
    MethodAspect::after(__METHOD__, [
      "${user['name']} added a complaint with id: ${complaint['id']}"
    ]);
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
    $aspect = new TransactionAspect();

    $aspect->register(function () use ($id, $user, $data, &$complaint) {
      $complaint = $this->complaints->find($id);

      if (!empty($complaint->editing_by) && $complaint->editing_by !== $user->name) {
        MethodAspect::after(__METHOD__, [
          "{$user->name} tried to edit {$complaint->id} that locked by {$complaint->editing_by}"
        ]);
        throw new \Exception("Cannot edit complaint {$complaint->id} as it is being edited by {$complaint->editing_by}");
      }

      $complaint = Complaint::lockForUpdate()->find($id);
      $complaint->editing_by = $user->name;
      $complaint->save();

      $userInfo['role'] = $user->role_id == 2 ? 'government' : 'employee';
      $userInfo['user_id'] = $user->id;

      $this->complaints->update($complaint, $data);
      $this->complaints->addComplaintLogs($complaint, $userInfo, $data);

      $complaint->editing_by = null;
      $complaint->save();
    });

    $aspect->registerAfterCommit(function () use ($complaint, $user) {
      MethodAspect::after(__METHOD__, [
        "complaint {$complaint?->id} was updated by {$user->name}"
      ]);
    });

    try {
      $aspect->commit();
      return response()->json([
        'success' => true,
        'message' => 'Complaint updated successfully'
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage()
      ], 423);
    }
  }
  public function getComplaintLog(int $id)
  {
    return $this->complaints->getComplaintLog($id);
  }
}
