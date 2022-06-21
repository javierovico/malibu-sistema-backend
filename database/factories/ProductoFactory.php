<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{

    protected $model = Producto::class;

    public function definition()
    {
        $name = $this->faker->unique()->name;
        $codigo = preg_replace("/[^a-z]/", '',$name) . $this->faker->randomNumber(5);
        $generadorPrecio = $this->faker->numberBetween(2,100);
        $costo = $generadorPrecio * 500;
        $precio = ((int)($generadorPrecio * (1+($this->faker->numberBetween(5,50))/100))) * 500;
        return [
            Producto::COLUMNA_NOMBRE => $name,
            Producto::COLUMNA_CODIGO => $codigo,
            Producto::COLUMNA_DESCRIPCION => $this->faker->text(),
            Producto::COLUMNA_PRECIO => $precio,
            Producto::COLUMNA_COSTO => $costo
        ];
    }
}
