<?php

namespace Database\Factories;

use App\Models\SubBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubbrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubBrand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode_sub_brand' => $this->faker->numerify('#T##ES'),
            'nama_sub_brand' => $this->faker->name(),
        ];
    }
}
