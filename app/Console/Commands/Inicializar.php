<?php

namespace App\Console\Commands;

use App\Exceptions\ExceptionSystem;
use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\TipoProducto;
use App\Models\Usuario;
use Database\Seeders\TipoProductoSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;

class Inicializar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inicializar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws ExceptionSystem
     */
    public function handle()
    {
        Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Rol::inicializar();
        $usuario = Usuario::nuevoUsuario('user1','user1', 'user1');
        $usuario->asignarRol(Rol::ROL_ADMIN_PRODUCTOS);
        $usuario->asignarRol(Rol::ROL_VISOR_INGRESOS);
        $usuario = Usuario::nuevoUsuario('operador','operador', 'operador');
        $usuario->asignarRol(Rol::ROL_OPERADOR);
        $usuario2 = Usuario::nuevoUsuario('user2','user2', 'user2');
        $usuario2->asignarRol(Rol::ROL_VISOR_INGRESOS);
        Artisan::call("db:seed");
        $tipoProductoCombo = TipoProducto::getTipoProductoCombo();
        Producto::factory()
            ->for($tipoProductoCombo)
            ->count(300)
            ->create()
        ;
        $tipoProductoSimple = TipoProducto::getTipoProductoSimple();
        Producto::factory()
            ->for($tipoProductoSimple)
            ->count(300)
            ->create()
        ;
        $productosSimples = Producto::getQueryProductoSimple()->get();
        Producto::getQueryProductoCombo()
            ->lazyById()
            ->each(fn (Producto $pC) => $productosSimples
                ->random(rand(3,8))   //toma de 2 a 5 productos aleatorios
                ->each(fn (Producto $pSimple) => $pC->productoCombos()->attach($pSimple))
            )
        ;
        Mesa::factory()->count(20)->create();
        Cliente::factory()->count(1500)->create();
        return 0;
    }
}
