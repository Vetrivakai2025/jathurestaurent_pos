<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // Insert some stuff
        DB::table('users')->insert(
            array(
                'id' => 1,
                'username' => 'William Castillo',
                'email' => 'vetrivakai@example.com',
                'password' => '$2y$10$d7ZA1uil1akMMBMDy4b68OMbjZY..xr5GwcZQL9rA/oVWq4FiE.de',
                'avatar' => 'no_avatar.png',
                'role_users_id' => 1,
                'is_all_warehouses' => 1,
                'status' => 1,
            )
        );
        $user = User::findOrFail(1);
        $user->assignRole(1);
    }
}
