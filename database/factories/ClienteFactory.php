<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            Cliente::COLUMNA_NOMBRE => $this->faker->name,
            Cliente::COLUMNA_CIUDAD => $this->faker->city,
            Cliente::COLUMNA_BARRIO => $this->faker->company,
            Cliente::COLUMNA_TELEFONO => $this->faker->phoneNumber,
            Cliente::COLUMNA_RUC => $this->faker->numberBetween(1000000,9999999) . '-' . $this->faker->numberBetween(0,10),
        ];
    }
}
