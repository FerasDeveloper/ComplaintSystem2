<?php

namespace App\Jobs;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\ComplaintRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class StoreAttachmentJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $filePath;
  protected $extension;
  protected $complaint_id;
  public $tries = 3;

  public function __construct(string $filePath, string $extension, int $complaint_id)
  {
    $this->filePath = $filePath;
    $this->extension = $extension;
    $this->complaint_id = $complaint_id;
  }

  public function handle(): void
  {
    try {
      $complaints = app(ComplaintRepositoryInterface::class);
      $complaint = $complaints->addAttachment($this->filePath, $this->extension, $this->complaint_id);

      if ($complaint) {
        $complaint->increment('processed_attachments');

        if ($complaint->processed_attachments >= $complaint->attachments_count) {
          $complaint->update(['status' => 'new']);
        }
      }
    } catch (Exception $e) {
      // إذا فشلت المحاولة، Laravel سيعيد تشغيل الـ Job تلقائيًا حتى 3 مرات
      Log::error("فشل تخزين المرفق للشكوى {$this->complaint_id}: " . $e->getMessage());
      throw $e; // لازم نرمي الاستثناء حتى يعيد Laravel المحاولة
    }
  }

  public function failed(Exception $exception): void
  {
    $complaint = Complaint::find($this->complaint_id);
    if ($complaint) {
      // $notification = Notification::create([
      //   'user_id' => $complaint->user_id,
        // 'message' => 'Failed to add your complaint because attachments upload failed. please try again',
      //   ])
      $complaint->delete();
    }
    Log::critical("فشل نهائي بعد 3 محاولات لتخزين المرفق للشكوى {$this->complaint_id}: " . $exception->getMessage());
  }
}
