<?php

namespace App\Http\Controllers;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{
   public function index() {
      $schools = School::with(['supervisor', 'school_type'])->orderBy('created_at', 'desc')->paginate(10);
      foreach ($schools as $s) {
         if ($s->updated_at) {
            $s->date = Carbon::parse($s->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
         }
      }
      return response()->json(['status' => 'success', 'data' => $schools]);
   }

   public function login(Request $request) {
      $cred = $this->validate($request, [
         'email' => 'required|email|exists:schools,email',
         'password' => 'required'
      ]);

      $school = School::where('email', $cred['email'])->first(); 
      if ($school && Hash::check($cred['password'], $school->password)) {
         $token = Str::random(40);
         $school->update(['api_token' => $token]);
         return response()->json(['status' => 'success', 'data' => $token]);
      }
      return response()->json(['status' => 'error']);
   }

   public function store(Request $request) {
      $cred = $this->validate($request, [
         'name' => 'required',
         'email' => 'required|email|unique:schools',
         'password' => 'required',
         'school_type_id' => 'required',
         'supervisor_id' => 'required',
      ]);

      $cred['password'] = Hash::make($cred['password']);

      School::create($cred);
      return response()->json(['status' => 'success']);
   }

   public function getSingle($id) {
      $school = School::find($id);
      return response()->json(['status' => 'success', 'data' => $school]);
   }

   public function update(Request $request, $id) {
      $school = School::find($id);

      // $password = $request->password != '' ? Hash::make($request->password) : $school->password; 

      $cred = $this->validate($request, [
         'name' => 'required',
         'email' => 'required|email|unique:schools,email,' . $id,
         'school_type_id' => 'required',
         'supervisor_id' => 'required',
         'principal' => 'nullable',
         'address' => 'nullable'
      ]);
      
      // $cred['password'] = $password;

      $school->update($cred);
      return response()->json(['status' => 'success']);
   }

   public function updatePassword(Request $request, $id) {
      $school = School::find($id);
      $old_password = $request->old_password;
      $password = $request->password;
      $confirm = $request->confirm_password;

      if (!Hash::check($old_password, $school->password)) {
         return response()->json(['status' => 'failed', 'message' => 'Kata sandi saat ini tidak cocok'], 422);
      }

      if ($password !== $confirm) {
         return response()->json(['status' => 'failed', 'message' => 'Konfirmasi kata sandi tidak cocok'], 422);
      }

      $hashed = Hash::make($password);
      $school->update(['password' => $hashed]);
      return response()->json(['status' => 'success']);
   }

   public function destroy($id) {
      School::destroy($id);
      return response()->json(['status' => 'success']);
   }

   public function getSchoolLogin(Request $request) {
      $data = School::with('supervisor')->where('id', $request->user()->id)->first();
      return response()->json(['status' => 'success', 'data' => $data]);
      // return response()->json(['status' => 'success', 'data' => $request->user()]);
   }

   public function logout(Request $request) {
      $school = $request->user();
      $school->update(['api_token' => null]);
      return response()->json(['status' => 'success']);
   }
}