<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\DataType;
use App\Models\DataStatus;
use App\Models\DataCategory;
use App\Models\Revision;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DataController extends Controller
{
   public function index() {
      $data = Data::all();
      return response()->json(['status' => 'success', 'data' => $data]);
   }
   
   public function getDataById($id) {
      $data = Data::find($id);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getDataBySchool($id) {
      $data = Data::with(['school', 'data_type', 'data_type.data_category', 'data_status'])->where('school_id', $id)->filter(request(['status', 'category', 'data_type']))->orderBy('updated_at', 'desc')->paginate(10);
      foreach($data as $d) {
         $d->type = $d->data_type->name;
         $d->status = $d->data_status->name;
         $d->category = $d->data_type->data_category->name;
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getDataBySupervisor($id) {
      $schools = School::where('supervisor_id', $id)->pluck('id');
      $data = Data::with(['school', 'data_type', 'data_type.data_category', 'data_status'])->whereIn('school_id', $schools)->filter(request(['status', 'category', 'data_type', 'school']))->orderBy('updated_at', 'desc')->paginate(10);
      foreach($data as $d) {
         // $d->school = $d->school->name;
         $d->data_category = $d->data_type->data_category;
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getVerifiedData() {
      $data = Data::with(['school', 'data_type', 'data_type.data_category', 'data_status'])->where('data_status_id', 2)->filter(request(['status', 'category', 'data_type', 'school']))->orderBy('updated_at', 'desc')->paginate(10);
      // foreach($data as $d) {
      //    $d->data_category = $d->data_type->data_category;
      // }
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

   public function edit($id) {
      $item = Data::find($id);
      $type = $item->data_type;
      $data_type = DataType::where('data_category_id', $type->data_category_id)->get();
      $category = DataCategory::all();
      $status = DataStatus::all();

      $data = [
         'data' => $item,
         'data_type' => $data_type,
         'category' => $category,
         'status' => $status
      ];
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
         'data_status_id' => $request->data_status_id,
         'data_type_id' => $request->data_type_id,
         'path' => $request->path,
         'year' => $request->year,
         'data_status_id' => 4,
      ]);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function delete(Request $request, $id) {
      $data = Data::destroy($id);
      if ($request->path && File::exists('files/'.$request->path)) {
         File::delete('files/'.$request->path);
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function downloadFile(Request $request) {
      $file_path = 'files/'.$request->path;

      if (File::exists($file_path)) {
         $file = File::get($file_path);
         $response = new Response($file, 200);
         return $response;
      } else return response()->json(['error' => 'File not found'], 404);
   }
}
