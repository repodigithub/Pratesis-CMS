<?php

namespace Database\Seeders;

use App\Models\Permission\Group;
use App\Models\Permission\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ResetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            Schema::disableForeignKeyConstraints();
            // Buat kode group / user level
            $groups = [
                [
                    "kode_group" => User::ROLE_DISTRIBUTOR,
                    "nama_group" => "Distributor",
                ],
                [
                    "kode_group" => User::ROLE_GENERAL_ADMIN,
                    "nama_group" => "General Admin",
                ],
                [
                    "kode_group" => User::ROLE_HEAD_OFFICE,
                    "nama_group" => "Head Office",
                ],
            ];

            // DB::table('group_permission')->truncate();
            Permission::truncate();
            Group::truncate();
            $res = Group::insert($groups);
            $this->command->info(sprintf("Create groups %s", $res));

            $objects = ["dashboard", "area", "promo", "user", "product"];
            $actions = ["create", "update", "delete", "view"];
            $permissions = [];
            foreach ($objects as $object) {
                foreach ($actions as $action) {
                    $permissions[] = [
                        "kode_permission" => "$action:$object",
                        "nama_permission" => "Can $action $object",
                    ];
                }
            }

            $res = Permission::insert($permissions);
            $this->command->info(sprintf("Create permission %s", $res));

            foreach ($groups as $group) {
                $g = Group::firstWhere('kode_group', $group['kode_group']);
                foreach ($permissions as $permission) {
                    $p = Permission::firstWhere('kode_permission', $permission['kode_permission']);
                    $g->permissions()->attach($p);
                }
            }

            User::truncate();
            // Buat admin
            $user = User::create([
                "user_id" => "ADM01",
                "full_name" => "Administrator",
                "email" => "admin@local.host",
                "password" => Hash::make('password'),
                "username" => "admin1",
                "kode_group" => User::ROLE_HEAD_OFFICE,
                "status" => User::STATUS_APPROVE,
            ]);
            $this->command->info("Create admin");

            Schema::enableForeignKeyConstraints();
        });
    }
}
