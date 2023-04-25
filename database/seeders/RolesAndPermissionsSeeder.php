<?php

namespace Database\Seeders;

use App\Models\Manager;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\{Role, Permission};

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::query()->delete();
        Permission::query()->delete();

        $disallowCatapultManagerPermissions = [
            'view team',
            'assign project member',
            'assign project manager',
            'assign custom project manager',
        ];

        $catapultManagerPermissions = [
            'delete activity',
            'delete ambassador',
            'view withdrawal requests',
            'approve withdrawal request',
            'decline withdrawal request',
            'level up',
            'create access',
        ];

        $projectOwnerPermissions = [
            'delete project',
            'edit project',
            'assign project administrator',
            'assign custom project member',
        ];

        $projectAdminPermissions = [
            'view team',
            'approve activity',
            'decline activity',
            'view ambassadors',
            'view ambassador',
            'delete task',
            'assign project member',
        ];

        $projectManagerPermissions = [
            'create task',
            'edit task',
            'approve task',
            'return task',
            'take on revision task',
        ];

        $permissions = array_map(function ($permission) {
            return [
                'name' => $permission,
                'guard_name' => 'api',
            ];
        }, array_merge(
            [
                'create project',
                'view accesses',
                'delete access',
                'update access',
            ], // Other permissions
            $catapultManagerPermissions,
            $projectOwnerPermissions,
            $projectAdminPermissions,
            $projectManagerPermissions,
        ));

        Permission::insert($permissions);

        // Global Roles
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Catapult Manager'])
            ->givePermissionTo(array_merge(
                $catapultManagerPermissions,
                array_filter($projectAdminPermissions, function ($name) use ($disallowCatapultManagerPermissions) {
                    return !in_array($name, $disallowCatapultManagerPermissions);
                }),
                $projectManagerPermissions,
            ));

        // Project Roles
        Role::create(['name' => 'Project Owner'])
            ->givePermissionTo(array_merge(
                $projectOwnerPermissions,
                $projectAdminPermissions,
                $projectManagerPermissions,
            ));

        Role::create(['name' => 'Project Administrator'])
            ->givePermissionTo(array_merge($projectAdminPermissions, $projectManagerPermissions));

        Role::create(['name' => 'Project Manager'])
            ->givePermissionTo($projectManagerPermissions);

        // 0 is temporary global? see discussion: https://github.com/spatie/laravel-permission/discussions/2088
        setPermissionsTeamId(0);
        Manager::firstWhere(['email' => 'admin@admin.com'])->assignRole($superAdminRole);
    }
}
