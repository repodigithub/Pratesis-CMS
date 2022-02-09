<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

class AreaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Area::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode_area' => $this->faker->numerify('#T##ES'),
            'nama_area' => $this->faker->name(),
            'alamat_depo' => $this->faker->name(),
            'kode_region' => 'TEST01',
        ];
    }
}
