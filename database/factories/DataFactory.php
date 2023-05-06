<?php

namespace Database\Factories;

use App\Models\Data;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataFactory extends Factory
{
   /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
   protected $model = Data::class;

   /**
    * Define the model's default state.
    *
    * @return array
    */
   public function definition()
   {
      return [
         'data_type_id' => mt_rand(1, 34),
         'school_id' => mt_rand(1, 2),
         'path' => $this->faker->sentence,
         'year' => '2022-2023',
         'data_status_id' => mt_rand(1, 4)
      ];
   }
}
