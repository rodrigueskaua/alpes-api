<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Gerar um external_id único antes de retornar
        $externalId = $this->faker->unique()->numberBetween(1000, 9999);

        return [
            'external_id' => $externalId,
            'type'        => $this->faker->word(),
            'brand'       => $this->faker->company(),
            'model'       => $this->faker->word(),
            'version'     => $this->faker->word(),
            'year_model'  => $this->faker->year(),
            'year_build'  => $this->faker->year(),
            'optionals'   => [],
            'doors'       => $this->faker->numberBetween(2, 5),
            'board'       => $this->faker->bothify('???-####'),
            'chassi'      => $this->faker->regexify('[A-Z0-9]{17}'),
            'transmission'=> $this->faker->word(),
            'km'          => $this->faker->numberBetween(0, 200000),
            'description' => $this->faker->sentence(),
            'sold'        => $this->faker->boolean(),
            'category'    => $this->faker->word(),
            'url_car'     => $this->faker->url(),
            'old_price'   => $this->faker->randomFloat(2, 10000, 200000),
            'price'       => $this->faker->randomFloat(2, 10000, 200000),
            'color'       => $this->faker->colorName(),
            'fuel'        => $this->faker->word(),
            'photos'      => [],
        ];
    }
}
