<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Revision;
use Illuminate\Http\Request;

class RevisionController extends Controller
{
   public function getRevision(Request $request) {
      $data = Revision::where('data_id', $request->id)->orderBy('updated_at', 'desc')->get();
      foreach ($data as $d) {
         $d->date = Carbon::parse($d->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
         // dd($d->updated_at);
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }
}
