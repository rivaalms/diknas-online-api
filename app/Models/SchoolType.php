<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolType extends Model
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
      return $this->hasMany(School::class);
   }
}