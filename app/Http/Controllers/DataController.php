<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Data;
use App\Models\School;
use App\Models\DataType;
use App\Models\Revision;
use App\Models\DataStatus;
use App\Models\DataCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DataController extends Controller
{
   public function index() {
      $data = Data::with(['data_type', 'data_status', 'data_type.data_category', 'school'])->filter(request(['school', 'status', 'category', 'data_type', 'year']))->orderBy('updated_at', 'desc')->paginate(10);
      foreach ($data as $d) {
         $d->date = Carbon::parse($d->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }
   
   public function getDataById($id) {
      $data = Data::with(['data_type', 'data_type.data_category', 'data_status', 'school'])->find($id);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getDataBySchool($id) {
      $data = Data::with(['school', 'data_type', 'data_type.data_category', 'data_status'])->where('school_id', $id)->filter(request(['status', 'category', 'data_type', 'year']))->orderBy('updated_at', 'desc')->paginate(10);
      foreach($data as $d) {
         $d->type = $d->data_type->name;
         $d->status = $d->data_status->name;
         $d->category = $d->data_type->data_category->name;
         $d->date = Carbon::parse($d->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getDataBySupervisor(Request $request) {
      $schools = School::where('supervisor_id', $request->supervisor)->pluck('id');
      $data = Data::with(['school', 'data_type', 'data_type.data_category', 'data_status'])->whereIn('school_id', $schools)->filter(request(['school', 'status', 'category', 'data_type', 'year']))->orderBy('updated_at', 'desc')->paginate(10);
      foreach($data as $d) {
         $d->date = Carbon::parse($d->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getVerifiedData() {
      $data = Data::with(['school', 'data_type', 'data_type.data_category', 'data_status'])->where('data_status_id', 2)->filter(request(['school', 'status', 'category', 'data_type', 'year']))->orderBy('updated_at', 'desc')->paginate(10);
      foreach($data as $d) {
         $d->date = Carbon::parse($d->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function verifyData(Request $request) {
      $data = Data::find($request->id);
      $data->update([
         'data_status_id' => 2
      ]);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function revisionData(Request $request) {
      $data = Data::find($request->id);
      $revision = Revision::create([
         'data_id' => $request->id,
         'note' => $request->revision_notes,
      ]);
      $data->update([
         'data_status_id' => 3
      ]);
      return response()->json(['status' => 'success', 'data' => $data, 'revision' => $revision]);
   }

   public function create(Request $request) {
      $data = Data::create($request->request->all());
      $file = $request->file('file');
      $fileName = $request->file('file')->getClientOriginalName();
      $file->move('files', $fileName);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function updateSchool(Request $request, $id) {
      $file = $request->file('file');
      $fileName = $file->getClientOriginalName();
      $file->move('files', $fileName);

      if ($request->old_path && File::exists('files/'.$request->old_path)) {
         File::delete('files/'.$request->old_path);
      }
      
      $data = Data::where('id', $id)->update([
         'data_type_id' => $request->data_type_id,
         'path' => $request->path,
         'year' => $request->year,
         'data_status_id' => 4,
      ]);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function update(Request $request, $id) {
      if ($request->file()) {
         $file = $request->file('file');
         $fileName = $file->getClientOriginalName();
         $file->move('files', $fileName);

         if ($request->old_path && File::exists('files/'.$request->old_path)) {
            File::delete('files/'.$request->old_path);
         }

         $data = Data::where('id', $id)->update([
            'data_status_id' => $request->data_status_id,
            'data_type_id' => $request->data_type_id,
            'path' => $request->path,
            'year' => $request->year,
         ]);
         return response()->json(['status' => 'success', 'data' => $data]);
      } else {
         return response()->json(['status' => 'failed', 'message' => 'File wajib dilampirkan'], 422);
      }
   }

   public function delete(Request $request, $id) {
      $data = Data::destroy($id);
      if ($request->path && File::exists('files/'.$request->path)) {
         File::delete('files/'.$request->path);
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function downloadFile(Request $request) {
      if (!$request->path) {
         return response()->json(['error' => 'Invalid parameters', 'status' => 422], 422);
      }
      $file_path = 'files/'.$request->path;

      if (File::exists($file_path)) {
         $file = File::get($file_path);
         $response = new Response($file, 200);
         return $response;
      } else return response()->json(['error' => 'File not found', 'status' => 404], 404);
   }

   public function searchSchoolFilter() {
      $data = School::get(['id', 'name']);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getDataYear() {
      $query = DB::table('data')->select('year');

      if (request()->header('user-type') == 1) {
         $data = $query->where('school_id', request()->user_id)->distinct()->orderBy('year', 'desc')->get();
      } else if (request()->header('user-type') == 2) {
         $schools = School::where('supervisor_id', request()->user_id)->pluck('id');
         $data = $query->whereIn('school_id', $schools)->distinct()->orderBy('year', 'desc')->get();
      } else if (request()->header('user-type') == 3) {
         $data = $query->where('data_status_id', 2)->distinct()->orderBy('year', 'desc')->get();
      } else if (request()->header('user-type') == 4) {
         $data = $query->distinct()->orderBy('year', 'desc')->get();
      }

      return response()->json(['status' => 'success', 'data' => $data]);
   }
}
