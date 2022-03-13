<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Brand;
use App\Models\BudgetHolder;
use App\Models\Category;
use App\Models\Distributor;
use App\Models\Permission\Group;
use App\Models\Permission\Permission;
use App\Models\Region;
use App\Models\DistributorGroup;
use App\Models\Divisi;
use App\Models\Investment;
use App\Models\Product;
use App\Models\Promo\Promo;
use App\Models\Spend;
use App\Models\SubBrand;
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
            Region::truncate();
            $region = Region::create([
                'kode_region' => 'TEST',
                'nama_region' => 'Tes region',
            ]);

            // Buat kode depot / area
            Area::truncate();
            $area = Area::create([
                'kode_area' => 'TEST',
                'nama_area' => 'Tes area',
                'alamat_depo' => 'Alamat tes area',
                'kode_region' => $region->kode_region,
            ]);

            // Buat sales
            DistributorGroup::truncate();
            $sales = DistributorGroup::create([
                'kode_distributor_group' => 'TEST',
                'nama_distributor_group' => 'Tes sales',
            ]);

            // Buat kode distributor
            Distributor::truncate();
            $distributor = Distributor::create([
                'kode_distributor' => 'TEST',
                'nama_distributor' => 'Test distributor',
                'kode_distributor_group' => $sales->kode_distributor_group,
                'kode_area' => $area->kode_area,
                'alamat' => 'alamat',
                'titik_koordinat' => null,
                'status_distributor' => Distributor::STATUS_ACTIVE,
            ]);

            // Buat admin
            User::truncate();
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

            Investment::create([
                "kode_investment" => "TEST",
                "nama_investment" => "Test investment"
            ]);
            Spend::create([
                "kode_spend_type" => "TEST",
                "kode_investment" => "TEST",
                "fund_type" => "1",
                "reference_tax" => "#",
                "condition_type" => "10"
            ]);
            BudgetHolder::create([
                "kode_budget_holder" => "TEST",
                "nama_budget_holder" => "Test budget"
            ]);
            SubBrand::create([
                "kode_sub_brand" => "TEST", "nama_sub_brand" => "Test sub brand"
            ]);
            Brand::create([
                "kode_brand" => "TEST", "nama_brand" => "Test brand"
            ]);
            Category::create([
                "kode_kategori" => "TEST",
                "nama_kategori" => "Test kategori"
            ]);
            Divisi::create([
                "kode_divisi" => "TEST",
                "nama_divisi" => "Test divisi"
            ]);
            for ($i = 0; $i < 5; $i++) {
                Product::create([
                    "kode_produk" => "TEST" . str_pad(($i + 1), 2, 0, STR_PAD_LEFT),
                    "nama_produk" => trim("Test produk " . ($i + 1)),
                    "kode_sub_brand" => "TEST",
                    "kode_brand" => "TEST",
                    "kode_kategori" => "TEST",
                    "kode_divisi" => "TEST"
                ]);
            }
            Promo::create([
                "status"=> Promo::STATUS_DRAFT,
                "opso_id" => "22030001",
                "nama_promo" => "Promo Ramadhan",
                "budget" => "100000000",
                "start_date" => "2022-04-01",
                "end_date" => "2022-05-01",
                "claim" => "7",
                "kode_spend_type" => "TEST",
                "kode_budget_holder" => "TEST",
                "file" => "/storage/promo/20220312/174427/quotation.pdf"
            ]);
        });
    }
}
