<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Supervisor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\SchoolStudentController;

class SupervisorController extends Controller
{
   /**
    * Create a new controller instance.
    *
    * @return void
    */

   public function login(Request $request) {
      $cred = $this->validate($request, [
         'nip' => 'required|exists:supervisors,nip',
         'password' => 'required'
      ]);

      $supervisor = Supervisor::where('nip', $cred['nip'])->first(); 
      if ($supervisor && Hash::check($cred['password'], $supervisor->password)) {
         $token = Str::random(40);
         $supervisor->update(['api_token' => $token]);
         return response()->json(['status' => 'success', 'data' => $token]);
      }
      return response()->json(['status' => 'error']);
   }

   public function logout(Request $request) {
      $supervisor = $request->user();
      $supervisor->update(['api_token' => null]);
      return response()->json(['status' => 'success']);
   }

   public function getSelf(Request $request) {
      $data = Supervisor::where('id', $request->user()->id)->first();
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getSchoolBySupervisor($id) {
      $data = School::where('supervisor_id', $id)->get();
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getPaginatedSchoolBySupervisor($id) {
      $school = School::where('supervisor_id', $id)->paginate(5);
      $schoolStudents = new SchoolStudentController;
      $schoolTeachers = new SchoolTeacherController;
      foreach ($school as $s) {
         $teachers = $schoolTeachers->getTeachersNotJSON($s->id);
         $s->total_teachers = $teachers->total_teachers;
         $s->teachers = $teachers->teachers;
         // dd($s->teachers);
         $students = $schoolStudents->getStudentsNotJSON($s->id);
         $s->total_students = $students->total_students;
         $s->students = $students->students;
      }
      return response()->json(['status' => 'success', 'data' => $school]);
   }

   public function updatePassword(Request $request, $id) {
      $supervisor = Supervisor::find($id);
      $old_password = $request->old_password;
      $password = $request->password;
      $confirm = $request->confirm_password;

      // dd($supervisor->password);

      if (!Hash::check($old_password, $supervisor->password)) {
         return response()->json(['status' => 'failed', 'message' => 'Kata sandi saat ini tidak cocok'], 422);
      }

      if ($password !== $confirm) {
         return response()->json(['status' => 'failed', 'message' => 'Konfirmasi kata sandi tidak cocok'], 422);
      }

      $hashed = Hash::make($password);
      $supervisor->update(['password' => $hashed]);
      return response()->json(['status' => 'success']);
   }
}
