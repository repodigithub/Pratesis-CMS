<?php

namespace App\Http\Controllers;

use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoBrand;
use App\Models\Promo\PromoProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoProductController extends Controller
{
    function __construct()
    {
        $this->middleware("auth:api");
    }

    public function index($id, Request $req)
    {
        $is_from_depot = strpos($req->getPathInfo(), 'promo-depot') !== false;

        if ($is_from_depot) {
            if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $data = $this->getModel(PromoArea::class, $id)->promo->promoProducts();
        } else {
            $data = $this->getModel(Promo::class, $id)->promoProducts();
        }
        $pagination = $this->getPagination($req);

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new PromoBrand())->getTable() . '.' . $sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        if ($is_from_depot) {
            $data->setCollection($data->getCollection()->makeHidden(['budget_brand']));
        }

        return $this->response($data);
    }

    public function create($id, Request $req)
    {
        $promo = $this->getModel(Promo::class, $id);

        $this->validate($req, PromoBrand::rules());

        $data = DB::transaction(function () use ($req, $promo) {
            $data = PromoBrand::create([
                'opso_id' => $promo->opso_id,
                'kode_brand' => $req->input('kode_brand'),
                'budget_brand' => $req->input('budget_brand'),
            ]);

            $this->fillProducts($data, $req);
            return $data;
        });
        $data = $this->getModel(PromoBrand::class, $data->id, 'products');

        return $this->response($data);
    }

    public function show($id, $product, Request $req)
    {
        $is_from_depot = strpos($req->getPathInfo(), 'promo-depot') !== false;
        if ($is_from_depot) {
            $promo_area = $this->getModel(PromoArea::class, $id);
        } else {
            $data = $this->getModel(Promo::class, $id);
        }
        
        $data = $this->getModel(PromoBrand::class, $product, 'products');
        if ($is_from_depot) {
            $data->makeHidden('budget_brand');
            $data->budget = $promo_area->budget * $data->persentase / 100;
            $data->products->makeHidden('budget_produk');
        }

        return $this->response($data);
    }

    public function update($id, $product, Request $req)
    {
        $promo = $this->getModel(Promo::class, $id);

        $data = $this->getModel(PromoBrand::class, $product);

        $this->validate($req, PromoBrand::rules($promo->opso_id));

        $data = DB::transaction(function () use ($req, $data) {
            $data->kode_brand = $req->input('kode_brand');
            $data->budget_brand = $req->input('budget_brand');
            $data->save();

            $this->fillProducts($data, $req);
            return $data;
        });
        $data = $this->getModel(PromoBrand::class, $product, 'products');

        return $this->response($data);
    }

    public function delete($id, $product, Request $req)
    {
        $data = $this->getModel(Promo::class, $id);
        $data = $this->getModel(PromoBrand::class, $product, 'products');

        $data->delete();

        return $this->response();
    }

    // 

    private function fillProducts(PromoBrand $promo_product, Request $req)
    {
        $promo = Promo::firstWhere('opso_id', $promo_product->opso_id);
        $input_products = collect($req->input('products'));

        $budget = $promo->budget;
        $budget_left = $budget - DB::table('promo_brand')->select(DB::raw('SUM(budget_brand)'))->where('opso_id', $promo->opso_id)->first()->sum;
        $budget_promo = $promo_product->budget_brand;
        $budget_input = $input_products->reduce(function ($carry, $item) {
            return $carry + $item['budget_produk'];
        }, 0);

        if ($budget_left < $budget_promo) {
            throw new BadRequestException("error_excess_budget");
        }

        if ($budget_promo != $budget_input) {
            throw new BadRequestException("error_budget_difference");
        }

        // delete all promo product by promo brand first
        PromoProduct::destroy($promo_product->products);

        // fill promo products
        $products = $promo_product->brand->products;
        foreach ($products as $product) {
            $input_product = $input_products->firstWhere('kode_produk', $product->kode_produk);
            $status = !empty($input_product) ? $input_product['status'] : 0;
            $budget = !empty($input_product) ? $input_product['budget_produk'] : 0;

            $promo_product->products()->create([
                'status' => $status,
                'kode_produk' => $product->kode_produk,
                'budget_produk' => $budget,
            ]);
        }
    }
}
