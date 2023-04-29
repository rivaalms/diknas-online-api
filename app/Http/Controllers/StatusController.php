<?php

namespace App\Http\Controllers;

use App\Models\DataStatus;

class StatusController extends Controller
{
   public function index() {
      $data = DataStatus::all();
      return response()->json(['status' => 'success', 'data' => $data]);
   }
}
