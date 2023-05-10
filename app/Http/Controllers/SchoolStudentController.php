<?php

namespace App\Http\Controllers;

use App\Models\SchoolStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolStudentController extends Controller
{
   public function getStudents($id) {
      $data = SchoolStudent::where('school_id', $id)->filter(request(['year']))->orderBy('updated_at', 'desc')->get();
      $data2 = [];
      $data = $data->toArray();
      $i = 0;
      while ($i < count($data)) {
         $j = count($data) - 1;
         while ($j > $i) {
            if ($data[$i]['grade'] === $data[$j]['grade']) {
               unset($data[$j]);
               $data = array_values($data);
            }
            $j--;
         }
         $i++;
      }

      foreach($data as $d) {
         array_push($data2, $d);
      }

      usort($data2, fn($a, $b) => $a['grade'] - $b['grade']);
      return response()->json(['status' => 'success', 'data' => $data2]);
   }

   public function getStudentsYear($id) {
      $query = DB::table('school_students')->select('year')->where('school_id', $id);
      $data = $query->distinct()->orderBy('year', 'desc')->get();
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function storeStudents(Request $request) {
      $data = SchoolStudent::create($request->all());
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getStudentsNotJSON($id) {
      $data = SchoolStudent::where('school_id', $id)->filter(request(['year']))->orderBy('updated_at', 'desc')->get();
      // dd($data);
      $data2 = [];
      $i = 0;
      
      // while ($i < count($data)) {
      //    $j = $i + 1;
      //    while ($j < count($data)) {
      //       if ($data[$i]->grade == $data[$j]->grade) {
      //          unset($data[$j]);
      //       }
      //       $j++;
      //    }
      //    $i++;
      // }

      for ($i = count($data) - 1; $i >= 0; $i--) {
         for ($j = $i - 1; $j >= 0; $j--) {
            if ($data[$i]->grade === $data[$j]->grade) {
               unset($data[$i]);
               break;
            }
         }
      }
      
      $total = 0;
      foreach($data as $d) {
         $array = json_decode($d);
         $sum = 0;
         foreach ($array as $i => $a) {
            if ($i !== 'id' && $i !== 'school_id' && $i !== 'grade' && $i !== 'year' && $i !== 'created_at' && $i !== 'updated_at') {
               $total += $a;
               $sum += $a;
            }
         }
         $d->total = $sum;
         array_push($data2, $d);
      }

      usort($data2, fn($a, $b) => $a->grade - $b->grade);
      // return response()->json(['status' => 'success', 'data' => $data2]);
      return (object) [
         'total_students' => $total,
         'students' => $data2
      ];
   }
}
