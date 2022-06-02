<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Brand;
use App\Models\BudgetHolder;
use App\Models\Category;
use App\Models\Claim;
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
use App\Models\Tax;
use App\Models\TipePromo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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
            // $distributor = Distributor::create([
            //     'kode_distributor' => 'TEST',
            //     'nama_distributor' => 'Test distributor',
            //     'kode_distributor_group' => $sales->kode_distributor_group,
            //     'kode_area' => $area->kode_area,
            //     'alamat' => 'alamat',
            //     'titik_koordinat' => null,
            //     'status_distributor' => Distributor::STATUS_ACTIVE,
            // ]);
            $distributors = [];
            for ($i = 0; $i < 5; $i++) {
                $distributors[] = Distributor::create([
                    'kode_distributor' => 'TEST' . $i,
                    'nama_distributor' => 'Test distributor ' . $i,
                    'kode_distributor_group' => $sales->kode_distributor_group,
                    'kode_area' => $area->kode_area,
                    'alamat' => null,
                    'titik_koordinat' => null,
                    'status_distributor' => Distributor::STATUS_ACTIVE,
                ]);
            }
            $this->command->info("create distributor");

            User::truncate();
            // Buat admin
            $user = User::create([
                "user_id" => "ADM01",
                "full_name" => "Administrator",
                "email" => "admin@local.host",
                "password" => Hash::make('password'),
                "username" => "admin1",
                "kode_group" => User::ROLE_HEAD_OFFICE,
                "kode_area" => $area->kode_area,
                "kode_distributor" => $distributors[0]->kode_distributor,
                "status" => User::STATUS_APPROVE,
            ]);
            $this->command->info("Create admin");
            // buat depot
            $ga = User::create([
                "user_id" => "GA01",
                "full_name" => "General Admin",
                "email" => "ga@local.host",
                "password" => Hash::make('password'),
                "username" => "ga01",
                "kode_group" => User::ROLE_GENERAL_ADMIN,
                "kode_area" => $area->kode_area,
                "status" => User::STATUS_APPROVE,
            ]);
            // buat distributor
            $dis = User::create([
                "user_id" => "DIS01",
                "full_name" => "Distributor",
                "email" => "dis@local.host",
                "password" => Hash::make('passw/ 100ord'),
                "username" => "dis01",
                "kode_group" => User::ROLE_DISTRIBUTOR,
                "kode_distributor" => $distributors[0]->kode_distributor,
                "status" => User::STATUS_APPROVE,
            ]);
            $this->command->info("Create admin distributor");

            Investment::truncate();
            $investment = Investment::create([
                "kode_investment" => "TEST",
                "nama_investment" => "Test investment"
            ]);
            $this->command->info("create investment");
            Spend::truncate();
            $spend = Spend::create([
                "kode_spend_type" => "TEST",
                "kode_investment" => "TEST",
                "fund_type" => "1",
                "reference_tax" => "#",
                "condition_type" => "10"
            ]);

            $this->command->info("create ppn");
            Tax::truncate();
            $ppn = Tax::create([
                'kode_pajak' => 'TESTPPN',
                'nama_pajak' => 'TEST PPN',
                'tipe_pajak' => 'ppn',
                'presentase_pajak' => 10,
                'reference_tax' => '#'
            ]);
            $this->command->info("create pph");
            $pph = Tax::create([
                'kode_pajak' => 'TESTPPH',
                'nama_pajak' => 'TEST PPH',
                'tipe_pajak' => 'pph',
                'presentase_pajak' => 10,
                'reference_tax' => '#'
            ]);
            $this->command->info("create tipe promo");
            $type = TipePromo::create([
                "kode_kegiatan" => "TEST",
                "nama_kegiatan" => "TEST Kegiatan",
                "deskripsi_kegiatan" => "TEST deskripsi kegiatan",
                "kode_ppn" => $ppn->kode_pajak,
                "kode_pph" => $pph->kode_pajak,
            ]);
            $type->spendTypes()->attach($spend);

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
            for ($i = 1; $i <= 5; $i++) {
                Brand::create([
                    "kode_brand" => "TEST0$i", "nama_brand" => "Test brand 0$i"
                ]);
            }
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
            foreach (Brand::all() as $index => $brand) {
                for ($i = 0; $i < 5; $i++) {
                    Product::create([
                        "kode_produk" => "TEST" . str_pad(($index * 5 + $i + 1), 2, 0, STR_PAD_LEFT),
                        "nama_produk" => trim("Test produk " . ($i + 1)),
                        "kode_sub_brand" => "TEST",
                        "kode_brand" => $brand->kode_brand,
                        "kode_kategori" => "TEST",
                        "kode_divisi" => "TEST"
                    ]);
                }
            }
            $this->command->info("create produk");
            Promo::truncate();
            PromoImage::truncate();
            PromoBrand::truncate();
            PromoProduct::truncate();
            PromoArea::truncate();
            PromoDistributor::truncate();

            $promos = [];
            for ($i = 0; $i < 10; $i++) {
                $sequenceName = (new Promo())->getTable() . "_id_seq";
                $count = DB::selectOne("SELECT nextval('{$sequenceName}') AS val")->val;
                $opso_id = (int) (date('y') . date('m') . str_pad($count, 4, 0, STR_PAD_LEFT));
                $status = [
                    Promo::STATUS_APPROVE,
                    Promo::STATUS_NEED_APPROVAL,
                    Promo::STATUS_APPROVE,
                    Promo::STATUS_DRAFT,
                    Promo::STATUS_APPROVE,
                ];

                $promos[] = Promo::create([
                    "status" => $status[$i % count($status)],
                    "opso_id" => $opso_id,
                    "nama_promo" => "Promo Ramadhan " . ($i + 1),
                    "budget" => "100000000",
                    "start_date" => "2022-04-01",
                    "end_date" => "2022-05-01",
                    "claim" => 14,
                    "kode_spend_type" => "TEST",
                    "kode_budget_holder" => "TEST",
                    "file" => "/storage/promo/20220312/174427/quotation.pdf"
                ]);
                $this->command->info("create promo");
            }

            $ind = 0;
            $ind_pa = 0;
            foreach ($promos as $promo) {
                PromoImage::create([
                    "opso_id" => $promo->opso_id,
                    "file" => "/storage/promo/image/20220316/020013/K.png",
                ]);
                $this->command->info("create promo image");
                foreach (Brand::all() as $index => $brand) {
                    $promo_brand = PromoBrand::create([
                        'opso_id' => $promo->opso_id,
                        'kode_brand' => $brand->kode_brand,
                        'budget_brand' => ($promo->budget * 10 / 100 + (10000 * $index)),
                        'method' => PromoBrand::METHOD_AUTO
                    ]);
                    foreach ($brand->products as $product) {
                        $budget = $promo_brand->budget_brand / $brand->products()->count();

                        $promo_brand->products()->create([
                            'status' => 1,
                            'kode_produk' => $product->kode_produk,
                            'budget_produk' => $budget,
                        ]);
                    }
                }
                $this->command->info("create promo product");
                $status = null;
                if ($promo->status == Promo::STATUS_APPROVE) {
                    $status = [PromoArea::STATUS_NEW_PROMO, PromoArea::STATUS_NEED_APPROVAL, PromoArea::STATUS_APPROVE,];
                    $status = $status[$ind_pa++ % count($status)];
                }
                $promo_area = PromoArea::create([
                    'opso_id' => $promo->opso_id,
                    'kode_area' => $area->kode_area,
                    'budget' => $promo->budget * 10 / 100,
                    'status' => $status,
                ]);
                $this->command->info("create promo area");

                $pds = [];
                $status = $promo_area->status == PromoArea::STATUS_APPROVE ? PromoDistributor::STATUS_APPROVE : null;
                foreach ($distributors as $d) {
                    $pds[] = PromoDistributor::create([
                        'promo_area_id' => $promo_area->id,
                        'kode_distributor' => $d->kode_distributor,
                        'budget' => $promo_area->budget * 4 / 25,
                        'status' => $status
                    ]);
                }
                $this->command->info('create promo distributor');

                $claims = [];
                for ($i = 0; $i < 3; $i++) {
                    $status = [Claim::STATUS_SUBMIT, Claim::STATUS_APPROVE, Claim::STATUS_DRAFT, Claim::STATUS_REJECT];
                    $item = $pds[$i];
                    $this->command->info($ind . ", " . $status[$ind % count($status)]);
                    $claim  = Claim::create([
                        'promo_distributor_id' => $item->id,
                        'kode_uli' => $item->kode_distributor . date('y') . str_pad(Claim::count() + 1, 4, 0, STR_PAD_LEFT),
                        'status' => $status[$ind++ % count($status)],
                        'amount' => $item->budget,
                        'laporan_tpr_barang' => '',
                        'laporan_tpr_uang' => '',
                        'faktur_pajak' => '',
                        'description' => '',
                    ]);
                    if ($claim->status == Claim::STATUS_APPROVE) {
                        $claim->approved_date = time();
                        $claim->save();
                    }
                    $claims[] = $claim;
                }
            }

            $claims = Claim::where('status', Claim::STATUS_APPROVE)->get();
            for ($i = 0; $i < $claims->count() / 2; $i++) {
                $claim = $claims[$i];
                $claim->bukti_bayar = 'contoh sudah terbayar';
                $claim->save();
            }

            $this->command->info(Claim::where('status', Claim::STATUS_REJECT)->count());

            Schema::enableForeignKeyConstraints();
        });
    }
}
