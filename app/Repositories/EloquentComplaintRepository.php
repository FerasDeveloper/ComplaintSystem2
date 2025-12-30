<?php

namespace App\Repositories;

use App\Jobs\SendOtpMailJob;
use App\Models\Complaint;
use App\Models\ComplaintLog;
use App\Models\ComplaintType;
use App\Models\Government;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use function Symfony\Component\Clock\now;

class EloquentComplaintRepository implements ComplaintRepositoryInterface
{
  public function create(array $data): Complaint
  {
    $complaint = Complaint::create($data);
    if ($complaint) {
      $complaint->load('attachments');
      Cache::put("complaint_{$complaint->id}", $complaint, \Carbon\Carbon::now()->addMinutes(10));
    }
    return $complaint;
  }

  public function addAttachment(string $path, string $extension, int $complaint_id): Complaint
  {
    $complaint = Complaint::findOrFail($complaint_id);
    $complaint->attachments()->create([
      'file_path' => $path,
      'file_type' => $extension,
    ]);

    $complaint->load('attachments');
    Cache::put("complaint_{$complaint_id}", $complaint, \Carbon\Carbon::now()->addMinutes(10));
    return $complaint;
  }

  public function find(int $id): Complaint
  {
    $user = Auth::user();
    return Cache::remember("complaint_{$id}", \Carbon\Carbon::now()->addMinutes(10), function () use ($id, $user) {
      $complaint = Complaint::with('attachments')->findOrFail($id);
      if ($user->role_id !== 4) {
        $complaint->load('logs');
        $complaint->logs->each(function ($log) {
          $log->user_name = User::find($log->user_id)->name;
        });
      }
      $complaint->attachments->makeHidden(['file_type', 'complaint_id', 'created_at', 'updated_at']);
      return $complaint;
    });
  }

  public function update(Complaint $complaint, array $data): void
  {
    $complaint->update($data);
    Notification::create([
      'user_id' => $complaint->user_id,
      'content' => "Your complaint #{$complaint->id} status has been updated to {$data['status']}",
    ]);
    SendOtpMailJob::dispatch(User::find($complaint->user_id), "Your complaint #{$complaint->id} status has been updated to {$data['status']}", 'Complaint Status Update');
    Cache::put("complaint_{$complaint->id}", $complaint, \Carbon\Carbon::now()->addMinutes(10));
  }

  public function addComplaintLogs(Complaint $complaint, array $userInfo, array $data): void
  {
    $complaint->logs()->create([
      'new_status' => $data['status'],
      'actor_type' => $userInfo['role'],
      'user_id' => $userInfo['user_id'],
      // 'update_date' => Carbon::now(),
      'note_content' => $data['note'] ?? null,
    ]);
  }

  public function getComplaints()
  {
    $u = Auth::user();
    $user = User::find($u->id);
    // return Cache::remember("complaints_user_{$user->id}", \Carbon\Carbon::now()->addMinutes(10), function () use ($user) {
    if ($user->role_id === 3) {
      $governmentId = $user->governments()->first()->id;
      return Complaint::with('attachments')->where('government_id', $governmentId)->latest()->get();
    } elseif ($user->role_id === 1) {
      return Complaint::with('attachments')->latest()->get();
    } elseif ($user->role_id === 2) {
      $governmentId = $user->governments()->first()->id;
      return Complaint::with('attachments')->where('government_id', $governmentId)->latest()->get();
    } elseif ($user->role_id === 4) {
      return Complaint::with('attachments')->where('user_id', $user->id)->latest()->get();
    }
    // });
  }
  public function getComplaintLog(int $id)
  {
    $u = Auth::user();
    $user = User::find($u->id);
    if ($user->role_id === 4) {
      $complaint = Complaint::with('attachments')->where('id', $id)->first();
      $complaint['type_name'] = ComplaintType::find($complaint->type_id)->name;
      $complaint['government_name'] = Government::find($complaint->government_id)->name;
      return $complaint;
    } else {
      $complaint = Complaint::with(['attachments', 'logs'])->where('id', $id)->first();
      $complaint['type_name'] = ComplaintType::find($complaint->type_id)->name;
      $complaint['government_name'] = Government::find($complaint->government_id)->name;
      return $complaint;
    }
  }

  public function getReports($data)
  {
    $user = Auth::user();
    $governmentId = $user->governments()->first()->id;
    $complaintsQueryIds = Complaint::query();

    if ($user->role_id === 3 || $user->role_id === 2) {
      $complaintsQueryIds->where('government_id', $governmentId);
    }

    $complaintsQuery = ComplaintLog::query()->whereIn('complaint_id', $complaintsQueryIds->pluck('id'));

    $time = $data['time'] ?? null;
    if ($time === 'daily' || $time === null) {
      $complaintsQuery->whereDate('created_at', Carbon::today());
    } elseif ($time === 'weekly') {
      $complaintsQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    } elseif ($time === 'monthly') {
      $complaintsQuery->whereMonth('created_at', Carbon::now()->month);
    } elseif ($time === 'yearly') {
      $complaintsQuery->whereYear('created_at', Carbon::now()->year);
    }
    return $complaintsQuery->latest()->get();
  }
}
