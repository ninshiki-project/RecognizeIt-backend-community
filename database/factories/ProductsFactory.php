<?php

namespace Database\Factories;

use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @method productName()
*/
class ProductsFactory extends Factory
{
    protected $model = Products::class;

    /**
     * @throws RandomException
     *
     */
    public function definition(): array
    {
        return [
            /** @phpstan-ignore-next-line  */
            'name' => $this->faker->productName(),
            'description' => $this->faker->text(),
            'price' => random_int(1000, 40000),
            'stock' => random_int(1, 50),
            'status' => collect(['available', 'unavailable'])->random(),
        ];
    }
}
