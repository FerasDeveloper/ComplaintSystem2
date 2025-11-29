<?php

namespace App\Aspects;

use Illuminate\Support\Facades\DB;

class TransactionAspect
{
  protected array $operations = [];
  protected array $afterCommitOperations = [];

  public function register(callable $operation): void
  {
    $this->operations[] = $operation;
  }

  public function registerAfterCommit(callable $operation): void
  {
    $this->afterCommitOperations[] = $operation;
  }

  public function commit()
  {
    return DB::transaction(function () {
      foreach ($this->operations as $operation) {
        $operation();
      }
      foreach ($this->afterCommitOperations as $operation) {
        DB::afterCommit($operation);
      }
    });
  }
}
