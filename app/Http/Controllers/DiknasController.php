<?php

namespace App\Http\Controllers;

use App\Models\Diknas;
use App\Models\School;
use App\Models\Supervisor;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DiknasController extends Controller
{
   /**
    * Create a new controller instance.
    *
    * @return void
    */

   public function index() {
      $data = Diknas::filter(request(['search']))->orderBy('updated_at', 'desc')->paginate(10);
      if ($data) {
         foreach($data as $d) {
            $d->date = Carbon::parse($d->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
         }
      }
      $count = Diknas::count();
      return response()->json(['status' => 'success', 'data' => $data, 'count' => $count]);
   }
   
   public function login(Request $request) {
      $cred = $this->validate($request, [
         'nip' => 'required|exists:diknas,nip',
         'password' => 'required'
      ]);

      $supervisor = Diknas::where('nip', $cred['nip'])->first(); 
      if ($supervisor && Hash::check($cred['password'], $supervisor->password)) {
         $token = Str::random(40);
         $supervisor->update(['api_token' => $token]);
         return response()->json(['status' => 'success', 'data' => $token]);
      }
      return response()->json(['status' => 'error']);
   }
   
   public function logout(Request $request) {
      $diknas = $request->user();
      $diknas->update(['api_token' => null]);
      return response()->json(['status' => 'success']);
   }

   public function getSelf(Request $request) {
      $data = Diknas::where('id', $request->user()->id)->first();
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getSchoolSupervisorCount() {
      $school = School::count();
      $supervisor = Supervisor::count();
      return response()->json(['status' => 'success', 'data' => [
         'school' => ['title' => 'Jumlah Sekolah', 'value' => $school], 'supervisor' => ['title' => 'Jumlah Pengawas', 'value' => $supervisor]]]);
   }

   public function getStudentTeacherYearList() {
      $studentController = new SchoolStudentController;
      $teacherController = new SchoolTeacherController;

      $studentYearList = $studentController->getAllStudentsYear();
      $teacherYearList = $teacherController->getAllTeachersYear();

      $studentYear = [];
      $teacherYear = [];
      foreach($studentYearList as $s) {
         array_push($studentYear, $s->year);
      }
      foreach($teacherYearList as $t) {
         array_push($teacherYear, $t->year);
      }

      $merged = array_merge($studentYear, $teacherYear);
      $data = array_unique($merged);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function getSchoolStats() {
      $school = School::with(['supervisor'])->filter(request(['school']))->paginate(10);
      $schoolStudents = new SchoolStudentController;
      $schoolTeachers = new SchoolTeacherController;

      foreach ($school as $s) {
         $teachers = $schoolTeachers->getTeachersNotJSON($s->id);
         $s->total_teachers = $teachers->total_teachers;
         $s->teachers = $teachers->teachers;

         $students = $schoolStudents->getStudentsNotJSON($s->id);
         $s->total_students = $students->total_students;
         $s->students = $students->students;
      }

      return response()->json(['status' => 'success', 'data' => $school]);
   }

   public function updatePassword(Request $request, $id) {
      $supervisor = Diknas::find($id);
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
         'email' => 'required|email|unique:diknas',
         'nip' => 'required|unique:diknas',
         'password' => 'required',
      ]);

      $cred['password'] = Hash::make($cred['password']);

      $data = Diknas::create($cred);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function update(Request $request, $id) {
      $diknas = Diknas::find($id);
      $cred = $this->validate($request, [
         'name' => 'required',
         'email' => 'required|email|unique:supervisors,email,' . $id,
         'nip' => 'required|unique:supervisors,nip,' . $id,
      ]);
      
      if ($request->password) {
         if (Hash::check($request->old_password, $diknas->password)) {
            $cred['password'] = Hash::make($request->password);
         }
      }

      $diknas->update($cred);
      return response()->json(['status' => 'success', 'data' => $diknas]);
   }

   public function delete($id) {
      $data = Diknas::destroy($id);
      return response()->json(['status' => 'success', 'data' => $data]);
   }
}
