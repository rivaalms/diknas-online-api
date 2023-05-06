<?php

namespace App\Models;

use Database\Factories\DataFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class Data extends Model
{
   // use Authenticatable, Authorizable, HasFactory;

   /**
    * The attributes that are mass assignable.
   *
   * @var string[]
   */
   // protected $fillable = [
   //    'name', 'email',
   // ];
   protected $guarded = ['id'];

   /**
    * The attributes excluded from the model's JSON form.
   *
   * @var string[]
   */

   protected static function factory(): Factory
   {
      return DataFactory::new();
   }
   
   public function data_type() {
      return $this->belongsTo(DataType::class);
   }

   public function school() {
      return $this->belongsTo(School::class);
   }

   public function data_status() {
      return $this->belongsTo(DataStatus::class);
   }

   public function revision() {
      return $this->hasMany(Revision::class);
   }

   public function scopeFilter($query, Array $filters) {
      $query->when($filters['status'] ?? false, function($query, $status) {
         return $query->where('data_status_id', $status);
      });
      $query->when($filters['category'] ?? false, function($query, $category) {
         $datatypes = DataType::where('data_category_id', $category)->pluck('id');
         return $query->whereIn('data_type_id', $datatypes);
      });
      $query->when($filters['data_type'] ?? false, function($query, $data_type) {
         return $query->where('data_type_id', $data_type);
      });
      $query->when($filters['school'] ?? false, function($query, $school) {
         return $query->where('school_id', $school);
      });
      $query->when($filters['year'] ?? false, function($query, $year) {
         return $query->where('year', 'like', '%'.$year.'%');
      });
   }
}
