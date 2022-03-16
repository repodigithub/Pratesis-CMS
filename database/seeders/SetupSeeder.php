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
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoBrand;
use App\Models\Promo\PromoDistributor;
use App\Models\Promo\PromoImage;
use App\Models\Promo\PromoProduct;
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
                    "kode_group" => User::ROLE_ADMINISTRATOR,
                    "nama_group" => "Administrator",
                ],
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
                [
                    "kode_group" => User::ROLE_SALES,
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
                        "kode_permission" => "$action:$object",
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
            $this->command->info("create region");

            // Buat kode depot / area
            Area::truncate();
            $area = Area::create([
                'kode_area' => 'TEST',
                'nama_area' => 'Tes area',
                'alamat_depo' => 'Alamat tes area',
                'kode_region' => $region->kode_region,
            ]);
            $this->command->info("create area");

            // Buat sales
            DistributorGroup::truncate();
            $sales = DistributorGroup::create([
                'kode_distributor_group' => 'TEST',
                'nama_distributor_group' => 'Tes sales',
            ]);
            $this->command->info("create distributor group");

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
            $this->command->info("create distributor");

            // Buat admin
            User::truncate();
            $user = User::create([
                "user_id" => "ADM01",
                "full_name" => "Administrator",
                "email" => "admin@local.host",
                "password" => Hash::make('password'),
                "username" => "admin1",
                "kode_group" => User::ROLE_ADMINISTRATOR,
                "kode_area" => null,
                "kode_distributor" => null,
                "status" => User::STATUS_APPROVE,
            ]);
            $this->command->info("Create admin");
            // buat distributor
            $dis = User::create([
                "user_id" => "DIS01",
                "full_name" => "Distributor",
                "email" => "dis@local.host",
                "password" => Hash::make('password'),
                "username" => "dis01",
                "kode_group" => User::ROLE_DISTRIBUTOR,
                "kode_area" => $area->kode_area,
                "kode_distributor" => $distributor->kode_distributor,
                "status" => User::STATUS_APPROVE,
            ]);
            $this->command->info("Create admin distributor");

            Investment::truncate();
            Investment::create([
                "kode_investment" => "TEST",
                "nama_investment" => "Test investment"
            ]);
            $this->command->info("create investment");
            Spend::truncate();
            Spend::create([
                "kode_spend_type" => "TEST",
                "kode_investment" => "TEST",
                "fund_type" => "1",
                "reference_tax" => "#",
                "condition_type" => "10"
            ]);
            BudgetHolder::truncate();
            BudgetHolder::create([
                "kode_budget_holder" => "TEST",
                "nama_budget_holder" => "Test budget"
            ]);
            $this->command->info("create budget holder");
            SubBrand::truncate();
            SubBrand::create([
                "kode_sub_brand" => "TEST", "nama_sub_brand" => "Test sub brand"
            ]);
            $this->command->info("create sub brand");
            Brand::truncate();
            $brand = Brand::create([
                "kode_brand" => "TEST", "nama_brand" => "Test brand"
            ]);
            $this->command->info("create brand");
            Category::truncate();
            Category::create([
                "kode_kategori" => "TEST",
                "nama_kategori" => "Test kategori"
            ]);
            $this->command->info("create kategori");
            Divisi::truncate();
            Divisi::create([
                "kode_divisi" => "TEST",
                "nama_divisi" => "Test divisi"
            ]);
            $this->command->info("create divisi");
            Product::truncate();
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
            $this->command->info("create produk");
            Promo::truncate();
            $promo = Promo::create([
                "status" => Promo::STATUS_DRAFT,
                "opso_id" => "22030001",
                "nama_promo" => "Promo Ramadhan",
                "budget" => "100000000",
                "start_date" => "2022-04-01",
                "end_date" => "2022-05-01",
                "claim" => 14,
                "kode_spend_type" => "TEST",
                "kode_budget_holder" => "TEST",
                "file" => "/storage/promo/20220312/174427/quotation.pdf"
            ]);
            $this->command->info("create promo");
            PromoImage::truncate();
            PromoImage::create([
                "opso_id" => $promo->opso_id,
                "file" => "/storage/promo/image/20220316/020013/K.png",
            ]);
            $this->command->info("create promo image");
            PromoBrand::truncate();
            $promo_brand = PromoBrand::create([
                'opso_id' => $promo->opso_id,
                'kode_brand' => $brand->kode_brand,
                'budget_brand' => $promo->budget * 10 / 100,
                'method' => PromoBrand::METHOD_AUTO
            ]);
            PromoProduct::truncate();
            foreach ($brand->products as $product) {
                $budget = $promo_brand->budget_brand / $brand->products()->count();

                $promo_brand->products()->create([
                    'status' => 1,
                    'kode_produk' => $product->kode_produk,
                    'budget_produk' => $budget,
                ]);
            }
            $this->command->info("create promo product");
            PromoArea::truncate();
            $promo_area = PromoArea::create([
                'opso_id' => $promo->opso_id,
                'kode_area' => $area->kode_area,
                'budget' => $promo->budget * 10 / 100,
                // 'status' => PromoArea::STATUS_NEW_PROMO
            ]);
            $this->command->info("create promo area");

            PromoDistributor::truncate();
            PromoDistributor::create([
                'promo_area_id' => $promo_area->id,
                'kode_distributor' => $distributor->kode_distributor,
                'budget' => $promo_area->budget * 20 / 100,
                // 'status' => PromoDistributor::STATUS_APPROVE
            ]);
        });
    }
}
