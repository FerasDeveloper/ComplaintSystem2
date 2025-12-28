<?php

use App\Models\Government;
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
    Schema::create('complaints', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('location');
      $table->string('description');
      $table->string('status')->default('waiting');
      $table->integer('attachments_count')->default(0);
      $table->integer('processed_attachments')->default(0);
      $table->string('editing_by')->nullable();
      $table->unique(['id', 'editing_by']);
      $table->foreignId('type_id')->constrained('complaint_types')->cascadeOnDelete()->cascadeOnUpdate();
      $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
      $table->foreignIdFor(Government::class)->constrained()->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('complaints');
  }
};
