<?php

namespace App\Aspects;

use Illuminate\Support\Facades\Log;

class MethodAspect
{
  // public static function before(string $method, array $data = [])
  // {
  //   Log::info(" قبل تنفيذ: {$method}", $data);
  // }

  public static function after(string $method, array $data = [])
  {
    Log::info("Method name: {$method}", $data);
  }
}
