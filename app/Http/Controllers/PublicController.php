<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{

  protected $publicService;
  public function __construct(\App\Services\PublicService $publicService)
  {
    $this->publicService = $publicService;
  }
  public function getGovernments()
  {
    return response()->json($this->publicService->getGovernments());
  }

  public function getComplaintTypes()
  {
    return response()->json($this->publicService->getComplaintTypes());
  }
  public function test()
  {
    return response()->json(['message' => 'dwdww']);
  }
}
