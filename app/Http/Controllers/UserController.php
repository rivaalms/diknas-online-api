<?php

namespace App\Http\Controllers;

use App\Models\Diknas;
use App\Models\User;
use App\Models\School;
use App\Models\Supervisor;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   // public function index() {
   //    $data = User::orderBy('created_at', 'desc')->paginate(10);
   //    return response()->json(['status' => 'success', 'data' => $schools]);
   // }

   public function index() {
      $data = User::filter(request(['search']))->orderBy('updated_at', 'desc')->paginate(10);
      if ($data) {
         foreach($data as $d) {
            $d->date = Carbon::parse($d->updated_at)->locale('id_ID')->translatedFormat('d F Y H:i');
         }
      }
      return response()->json(['status' => 'success', 'data' => $data]);
   }
   
   public function login(Request $request) {
      $cred = $this->validate($request, [
         'email' => 'required|email|exists:users,email',
         'password' => 'required'
      ]);

      $user = User::where('email', $cred['email'])->first(); 
      if ($user && Hash::check($cred['password'], $user->password)) {
         $token = Str::random(40);
         $user->update(['api_token' => $token]);
         return response()->json(['status' => 'success', 'data' => $token]);
      }
      return response()->json(['status' => 'error']);
   }

   public function getSelf(Request $request) {
      $data = User::where('id', $request->user()->id)->first();
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function updatePassword(Request $request, $id) {
      $user = User::find($id);
      $old_password = $request->old_password;
      $password = $request->password;
      $confirm = $request->confirm_password;

      if (!Hash::check($old_password, $user->password)) {
         return response()->json(['status' => 'failed', 'message' => 'Kata sandi saat ini tidak cocok'], 422);
      }

      if ($password !== $confirm) {
         return response()->json(['status' => 'failed', 'message' => 'Konfirmasi kata sandi tidak cocok'], 422);
      }

      $hashed = Hash::make($password);
      $user->update(['password' => $hashed]);
      return response()->json(['status' => 'success']);
   }


   public function logout(Request $request) {
      $user = $request->user();
      $user->update(['api_token' => null]);
      return response()->json(['status' => 'success']);
   }

   public function countUsers() {
      $school = School::count();
      $supervisor = Supervisor::count();
      $diknas = Diknas::count();
      $total = $school + $supervisor + $diknas;
      $data = [
         'total_school' => $school,
         'total_supervisor' => $supervisor,
         'total_diknas' => $diknas,
         'total' => $total
      ];
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function countSupervisors() {
      $data = Supervisor::count();
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function store(Request $request) {
      $cred = $this->validate($request, [
         'name' => 'required',
         'email' => 'required|email|unique:users',
         'password' => 'required',
      ]);

      $cred['password'] = Hash::make($cred['password']);

      $data = User::create($cred);
      return response()->json(['status' => 'success', 'data' => $data]);
   }

   public function update(Request $request, $id) {
      $admin = User::find($id);
      $cred = $this->validate($request, [
         'name' => 'required',
         'email' => 'required|email|unique:users,email,' . $id,
      ]);

      if ($request->password) {
         if (Hash::check($request->old_password, $admin->password)) {
            $cred['password'] = Hash::make($request->password);
         }
      }

      $admin->update($cred);
      return response()->json(['status' => 'success', 'data' => $admin]);
   }

   public function delete($id) {
      $admin = User::destroy($id);
      return response()->json(['status' => 'success', 'data' => $admin]);
   }
}