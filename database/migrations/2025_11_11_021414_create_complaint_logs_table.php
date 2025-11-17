<?php

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('complaint_logs', function (Blueprint $table) {
      $table->id();
      $table->string('new_status');
      $table->string('note_content')->nullable();
      $table->string('actor_type');
      $table->foreignIdFor(Complaint::class);
      $table->foreignIdFor(User::class);
      // $table->dateTime('update_date');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('complaint_logs');
  }
};
