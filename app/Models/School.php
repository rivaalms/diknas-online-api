<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class School extends Model implements AuthenticatableContract, AuthorizableContract
{
   use Authenticatable, Authorizable, HasFactory;

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
   protected $hidden = [
      'password',
      'api_token'
   ];

   public function supervisor() {
      return $this->belongsTo(Supervisor::class);
   }

   public function school_type() {
      return $this->belongsTo(SchoolType::class);
   }

   public function school_students() {
      return $this->hasMany(SchoolStudent::class);
   }

   public function school_teacher() {
      return $this->hasMany(SchoolTeacher::class);
   }

   public function data() {
      return $this->hasMany(Data::class);
   }

   public function scopeFilter($query, Array $filters) {
      $query->when($filters['school_type'] ?? false, function($query, $school_type) {
         return $query->where('school_type_id', $school_type);
      });
      $query->when($filters['name'] ?? false, function($query, $name) {
         return $query->where('name', 'like', '%'.$name.'%');
      });
      $query->when($filters['school'] ?? false, function($query, $school) {
         return $query->where('id', $school);
      });
   }
}
