<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Resetear la caché de roles y permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Lista de permisos (Duplicados eliminados)
        $permissions = [
            // Presupuestos
            'Ver presupuestos',
            'Crear presupuestos',
            'Eliminar presupuestos',

            // Clientes
            'Ver clientes',
            'Crear clientes',
            'Editar clientes',
            'Crear etiquetas de clientes',

            // Seguimiento Integral
            'Ver seguimiento integral',
            'Eliminar seguimiento integral',
            'Enviar correos en seguimiento integral',
            'Eliminar correos en seguimiento integral',
            'Registrar pagos en seguimiento integral',
            'Eliminar pagos en seguimiento integral',
            'Editar pagos en seguimiento integral',
            'Agendar citas en seguimiento integral',
            'Editar citas en seguimiento integral',
            'Eliminar citas en seguimiento integral',

            // Tickets
            'Ver tickets',
            'Crear tickets',
            'Editar tickets',
            'Eliminar tickets',

            // Administración de Usuarios/Sistema
            'Ver usuarios',
            'Ver roles y permisos',
        ];

        // 3. Crear permisos
        // Usamos firstOrCreate para evitar errores si se corre el seeder dos veces
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 4. Crear Rol Admin y asignar todos los permisos
        $role = Role::firstOrCreate(['name' => 'admin']);
        
        // Sincronizar permisos (obtiene todos los permisos de la base de datos y se los da al admin)
        $role->syncPermissions(Permission::all());

        // Opcional: Crear un usuario de prueba asignado al rol (comentar si no se requiere)
        /*
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password')
        ]);
        $user->assignRole($role);
        */
    }
}