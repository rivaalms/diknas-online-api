<?php

namespace App\Http\Controllers;

use App\Models\DataType;
use App\Models\DataCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
   public function index() {
      $categories = DataCategory::all();
      return response()->json(['status' => 'success', 'data' => $categories]);
   }

   // public function getDataTypes($slug) {
   //    $category = DataCategory::where('slug', $slug)->first();
   //    $data_types = DataType::where('data_category_id', $category->id)->get();
   //    return response()->json(['status' => 'success', 'data' => $data_types]);
   // }

   public function getDataTypes(Request $request) {
      $category = DataCategory::where('slug', $request->slug)->first();
      $data_types = DataType::filter($category)->get();
      return response()->json(['status' => 'success', 'data' => $data_types]);
   }
}
