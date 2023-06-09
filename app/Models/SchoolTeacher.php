<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolTeacher extends Model
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

   public function schools() {
      return $this->belongsTo(School::class);
   }

   public function scopeFilter($query, Array $filters) {
      $query->when($filters['year'] ?? false, function($query, $year) {
         return $query->where('year', $year);
      });
   }
}