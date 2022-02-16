<?php

namespace Database\Factories;

use App\Models\Spend;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpendFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Spend::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode_spend_type' => $this->faker->lexify('C??'),
            'kode_investment' => 'ivvb',
            'fund_type' => $this->faker->randomNumber(2, true),
            'reference_tax' => $this->faker->lexify(),
            'condition_type' => $this->faker->lexify(),

        ];
    }
}
