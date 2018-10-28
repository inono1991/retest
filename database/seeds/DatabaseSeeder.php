<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        DB::table('roles')->insert([
//            ['name' => 'administrators', 'level' => 1, 'describe' => '超级管理员', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
//            ['name' => 'groups', 'level' => 2, 'describe' => '组用户', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
//            ['name' => 'users', 'level' => 3, 'describe' => '普通用户', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
//            ['name' => 'guests', 'level' => 4, 'describe' => '游客', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
//        ]);
//        DB::table('permissions')->insert([
//            ['name' => 'insert', 'describe' => '写', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
//            ['name' => 'read', 'describe' => '读', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
//            ['name' => 'update', 'describe' => '修改', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
//            ['name' => 'delete', 'describe' => '删除', 'status' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
//        ]);

        $roles = DB::table('roles')->where('status', 1)->pluck('id', 'name')->toArray();
        $permissions = DB::table('permissions')->where('status', 1)->pluck('id', 'name')->toArray();
//        DB::table('role_permission')->insert([
//            ['role_id' => $roles['administrators'], 'permission_id' => $permissions['insert']],
//            ['role_id' => $roles['administrators'], 'permission_id' => $permissions['read']],
//            ['role_id' => $roles['administrators'], 'permission_id' => $permissions['update']],
//            ['role_id' => $roles['administrators'], 'permission_id' => $permissions['delete']],
//            ['role_id' => $roles['groups'], 'permission_id' => $permissions['insert']],
//            ['role_id' => $roles['groups'], 'permission_id' => $permissions['read']],
//            ['role_id' => $roles['groups'], 'permission_id' => $permissions['update']],
//            ['role_id' => $roles['users'], 'permission_id' => $permissions['insert']],
//            ['role_id' => $roles['users'], 'permission_id' => $permissions['read']],
//            ['role_id' => $roles['guests'], 'permission_id' => $permissions['read']],
//        ]);
        $roleIds = array_values($roles);
        for ($i = 0; $i < 30; $i++) {
            $user = new \App\User();
            $name = str_random(10);
            $user->name = $name;
            $user->email = $name . "@retest.com";
            $user->password = bcrypt('secret');
            $user->save();
            $userRoleIds = array_rand(array_flip($roleIds), rand(1, count($roleIds)));
            $userPermissions = DB::table('role_permission')->where(function ($query) use ($userRoleIds) {
                if (is_array($userRoleIds)) {
                    $query->whereIn('role_id', $userRoleIds);
                } else {
                    $query->where('role_id', $userRoleIds);
                }
            })->select('role_id', 'permission_id')->get()->toArray();
            if (is_array($userRoleIds)) {
                foreach ($userRoleIds as $id) {
                    DB::table('user_role')->insert(['user_id' => $user->id, 'role_id' => $id]);
                }
            }
//            $userPermissions = array_unique($userPermissions);
            foreach ($userPermissions as $permission) {
                DB::table('user_permission')->insert(['user_id' => $user->id, 'role_id' => $permission->role_id, 'permission_id' => $permission->permission_id]);
            }
        }
    }
}
