<?php

namespace Database\Seeders;

use App\Models\Depot;
use App\Models\Distributor;
use App\Models\Permission\Group;
use App\Models\Permission\Permission;
use App\Models\Region;
use App\Models\Sales;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            // Buat kode group / user level
            $groups = [
                [
                    "kode_group" => "AD",
                    "nama_group" => "Administrator",
                ],
                [
                    "kode_group" => "DI",
                    "nama_group" => "Distributor",
                ],
                [
                    "kode_group" => "GA",
                    "nama_group" => "General Admin",
                ],
                [
                    "kode_group" => "HO",
                    "nama_group" => "Head Office",
                ],
                [
                    "kode_group" => "SA",
                    "nama_group" => "Sales",
                ],
            ];

            Group::truncate();
            $res = Group::insert($groups);
            $this->command->info(sprintf("Create groups %s", $res));

            $objects = ["dashboard", "area", "promo", "user", "product"];
            $actions = ["create", "update", "delete", "view"];
            $permissions = [];
            foreach ($objects as $object) {
                foreach ($actions as $action) {
                    $permissions[] = [
                        "kode_permission" => "$object:$action",
                        "nama_permission" => "Can $action $object",
                    ];
                }
            }

            Permission::truncate();
            $res = Permission::insert($permissions);
            $this->command->info(sprintf("Create permission %s", $res));

            foreach ($groups as $group) {
                $g = Group::firstWhere('kode_group', $group['kode_group']);
                foreach ($permissions as $permission) {
                    $p = Permission::firstWhere('kode_permission', $permission['kode_permission']);
                    $g->permissions()->attach($p);
                }
            }

            // Buat region
            $region = Region::create([
                'kode_region' => 'TEST01',
                'nama_region' => 'Tes region',
            ]);

            // Buat kode depot / area
            $depot = Depot::create([
                'kode_area' => 'TEST01',
                'nama_area' => 'Tes area',
                'alamat_depo' => 'Alamat tes area',
                'kode_region' => $region->kode_region,
            ]);

            // Buat sales
            $sales = Sales::create([
                'kode_sales_workforce' => 'TEST01',
                'nama_sales_workforce' => 'Tes sales',
            ]);

            // Buat kode distributor
            $distributor = Distributor::create([
                'kode_distributor' => 'TEST01',
                'nama_distributor' => 'Test distributor',
                'kode_sales_workforce' => $sales->kode_sales_workforce,
                'kode_area' => $depot->kode_area,
                'kode_region' => $region->kode_region,
                'status_distributor' => Distributor::STATUS_ACTIVE,
            ]);

            // Buat admin
            $user = User::create([
                "user_id" => "ADM01",
                "full_name" => "Administrator",
                "email" => "admin@local.host",
                "password" => Hash::make('password'),
                "username" => "admin1",
                "kode_group" => "AD",
                "kode_area" => null,
                "kode_distributor" => null,
                "status" => User::STATUS_APPROVE,
            ]);
            $this->command->info(sprintf("Create admin %s", $user));
        });
    }
}
