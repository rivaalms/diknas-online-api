<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataType extends Model
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

   public function data() {
      return $this->hasMany(Data::class);
   }

   public function data_category() {
      return $this->belongsTo(DataCategory::class);
   }

   public function scopeFilter($query, $filters) {
      // dd($filters['id']);
      $query->when($filters['id'] ?? false, function($query, $category) {
         return $query->where('data_category_id', $category);
      });
   }
}
