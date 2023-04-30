<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Diknas extends Model implements AuthenticatableContract, AuthorizableContract
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
      'api_token',
   ];
}
