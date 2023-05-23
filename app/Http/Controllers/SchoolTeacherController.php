<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolTeacher;
use Illuminate\Support\Facades\DB;

class SchoolTeacherController extends Controller
{
   public function getTeachers(Request $request) {
      $data = SchoolTeacher::where('school_id', $request->school)->filter(request(['year']))->orderBy('updated_at', 'desc')->first();
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getTeachersYear(Request $request) {
      $query = DB::table('school_teachers')->select('year')->where('school_id', $request->school);
      $data = $query->distinct()->orderBy('year', 'desc')->get();
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getAllTeachersYear() {
      $data = DB::table('school_teachers')->select('year')->distinct()->orderBy('year', 'desc')->get();
      return $data;
   }

   public function storeTeachers(Request $request) {
      $data = SchoolTeacher::create($request->all());
      return response()->json(['status' => 'success', 'data' => $data]);
      // dd($request);
   }

   public function getTeachersNotJSON($id) {
      $data = SchoolTeacher::where('school_id', $id)->filter(request(['year']))->orderBy('updated_at', 'desc')->first();
      // return response()->json(['status' => 'success', 'data' => $data]);
      $total = 0;
      $array = json_decode($data);
      if ($array) {
         foreach ($array as $i => $a) {
            if ($i !== 'id' && $i !== 'school_id' && $i !== 'year' && $i !== 'created_at' && $i !== 'updated_at') {
               $total += $a;
            }
         }
      }
      return (object) [
         'total_teachers' => $total,
         'teachers' => $array
      ];
   }
}
