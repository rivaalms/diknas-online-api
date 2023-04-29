<?php

namespace App\Http\Controllers;

use App\Models\Revision;
use Carbon\Carbon;

class RevisionController extends Controller
{
   public function getRevisionData($id) {
      $data = Revision::where('data_id', $id)->orderBy('updated_at', 'desc')->get();
      foreach ($data as $d) {
         $d->date = Carbon::parse($d->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
         // dd($d->updated_at);
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }
}
