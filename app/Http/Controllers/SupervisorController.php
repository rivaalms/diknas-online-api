<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Supervisor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\SchoolStudentController;
use Carbon\Carbon;

class SupervisorController extends Controller
{
   /**
    * Create a new controller instance.
    *
    * @return void
    */

   public function index() {
      $data = Supervisor::filter(request(['search']))->orderBy('updated_at', 'desc')->paginate(10);
      foreach ($data as $d) {
         $d->date = Carbon::parse($d->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
      }
      $count = Supervisor::count();
      return response()->json(['status' => 'success', 'data' => $data, 'count' => $count]);
   }

   public function getAll() {
      $data = Supervisor::filter(request(['search', 'name']))->orderBy('name', 'asc')->get();
      return response()->json(['status' => 'success', 'data' => $data]);
   }
   
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
      $data = School::where('supervisor_id', $id)->filter(request(['school']))->get();
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getPaginatedSchoolBySupervisor($id) {
      $school = School::where('supervisor_id', $id)->filter(request(['school', 'year']))->paginate(5);
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

   public function store(Request $request) {
      $cred = $this->validate($request, [
         'name' => 'required',
         'email' => 'required|email|unique:supervisors',
         'nip' => 'required|unique:supervisors',
         'password' => 'required',
      ]);

      $cred['password'] = Hash::make($cred['password']);

      $data = Supervisor::create($cred);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function update(Request $request, $id) {
      $supervisor = Supervisor::find($id);
      $cred = $this->validate($request, [
         'name' => 'required',
         'email' => 'required|email|unique:supervisors,email,' . $id,
         'nip' => 'required|unique:supervisors,nip,' . $id,
      ]);

      if ($request->password) {
         if (Hash::check($request->old_password, $supervisor->password)) {
            $cred['password'] = Hash::make($request->password);
         }
      }

      $supervisor->update($cred);
      return response()->json(['status' => 'success', 'data' => $supervisor]);
   }

   public function delete($id) {
      $supervisor = Supervisor::destroy($id);
      return response()->json(['status' => 'success', 'data' => $supervisor]);
   }
}
