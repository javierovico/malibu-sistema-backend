<?php

namespace App\Console\Commands;

use App\Exceptions\ExceptionSystem;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Console\Command;
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
        $usuario2 = Usuario::nuevoUsuario('user2','user2', 'user2');
        $usuario2->asignarRol(Rol::ROL_VISOR_INGRESOS);
//        Producto::inicializar();
        Producto::factory()->count(1500)->create();
        return 0;
    }
}
