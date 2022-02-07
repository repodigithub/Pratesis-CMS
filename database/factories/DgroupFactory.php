<?php

namespace Database\Factories;

use App\Models\DistributorGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistributorGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DistributorGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode_distributor_group'=> $this->faker->numerify('TEST##'),
            'nama_distributor_group' => $this->faker->name
        ];
    }
}
