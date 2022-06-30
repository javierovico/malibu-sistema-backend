<?php

namespace Database\Factories;

use App\Models\Mesa;
use Illuminate\Database\Eloquent\Factories\Factory;

class MesaFactory extends Factory
{
    protected $model = Mesa::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            Mesa::COLUMNA_CODE => $this->faker->unique()->lexify('mesa-??'),
            Mesa::COLUMNA_ACTIVO => $this->faker->boolean(70),
            Mesa::COLUMNA_DESCRIPCION => $this->faker->text(30),
        ];
    }
}
