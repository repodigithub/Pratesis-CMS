<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kode_produk' => $this->faker->numerify('#T##ES'),
            'nama_produk' => $this->faker->name(),
            'kode_sub_brand' =>'8T67ES',
            'kode_brand' => 'B03',
            'kode_kategori' => 'Cbaru',
            'kode_divisi' => 'D8',
        ];
    }
}
