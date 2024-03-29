<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use App\Models\Version;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Daniel Elebash',
            'email' => 'danelebash@hotmail.com',
            'password' => bcrypt('Test12345')
        ]);

        $role = Role::create(['name' => 'admin']);
        $role2 = Role::create(['name' => 'user']);
        $permissions = [
            'post-list',
            'post-create',
            'post-edit',
            'post-delete',
            'review-list',
            'review-create',
            'review-edit',
            'review-delete',
            'category-list',
            'category-create',

            ];
        $role2->syncPermissions($permissions);
        Category::create(['name' => 'material']);
        Category::create(['name' => 'landscape']);

        Version::create(['name' => '4.6']);
        Version::create(['name' => '4.7']);
        Version::create(['name' => '5.0']);
        Version::create(['name' => '5.1']);
        Version::create(['name' => '5.2']);
        Version::create(['name' => '5.3']);
        Version::create(['name' => '5.4']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
