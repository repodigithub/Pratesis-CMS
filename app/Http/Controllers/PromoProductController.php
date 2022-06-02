<?php

namespace App\Http\Controllers;

use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoBrand;
use App\Models\Promo\PromoDistributor;
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
        $is_from_distributor = strpos($req->getPathInfo(), 'promo-distributor') !== false;

        if ($is_from_depot) {
            // if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $promo = $this->getModel(PromoArea::class, $id);
            $data = $promo->promo->promoProducts();
        } else if ($is_from_distributor) {
            // if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $promo = $this->getModel(PromoDistributor::class, $id);
            $data = $promo->promo->promoProducts();
        } else {
            $promo = $this->getModel(Promo::class, $id);
            $data = $promo->promoProducts();
        }
        $pagination = $this->getPagination($req);

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new PromoBrand())->getTable() . '.' . $sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        if ($is_from_depot || $is_from_distributor) {
            $data->setCollection(
                $data->getCollection()
                    ->makeHidden(['budget_brand'])
                    ->map(function ($val) use ($promo, $is_from_depot, $is_from_distributor) {
                        $val->budget = number_format($val->persentase * $promo->budget / 100, 2);
                        return $val;
                    })
            );
        }

        return $this->response($data);
    }

    public function create($id, Request $req)
    {
        $promo = $this->getModel(Promo::class, $id);

        $this->validate($req, PromoBrand::rules($promo));

        $data = DB::transaction(function () use ($req, $promo) {
            $data = $promo->promoProducts()->create($req->all());
            $this->fillProducts($data, $req);
            return $data;
        });
        $data = $this->getModel(PromoBrand::class, $data->id, 'products');

        return $this->response($data);
    }

    public function show($id, $product, Request $req)
    {
        $is_from_depot = strpos($req->getPathInfo(), 'promo-depot') !== false;
        $is_from_distributor = strpos($req->getPathInfo(), 'promo-distributor') !== false;

        $data = $this->getModel(PromoBrand::class, $product, 'products');

        if ($is_from_depot) {
            // if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $promo_area = $this->getModel(PromoArea::class, $id);
            $data->budget = number_format($promo_area->budget * $data->persentase / 100, 2);
        } else if ($is_from_distributor) {
            // if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $promo_distributor = $this->getModel(PromoDistributor::class, $req->route('id'));
            $data->budget = number_format($promo_distributor->budget * $data->persentase / 100, 2);
        }

        if ($is_from_depot || $is_from_distributor) {
            $data->makeHidden('budget_brand');
            $data->products = ($data->products
                ->makeHidden('budget_produk')
                ->map(function ($val) use ($data) {
                    $val->budget = number_format($val->persentase * filter_var($data->budget, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) / 100, 2);
                    return $val;
                })
            );
        }

        return $this->response($data);
    }

    public function update($id, $product, Request $req)
    {
        $promo = $this->getModel(Promo::class, $id);

        $data = $this->getModel(PromoBrand::class, $product);

        $this->validate($req, PromoBrand::rules($promo, $data));

        $data = DB::transaction(function () use ($req, $data) {
            $data->update($req->all());
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
        $budget_promo = $promo_product->budget_brand;
        $budget_input = $input_products->reduce(function ($carry, $item) {
            return $carry + $item['budget_produk'];
        }, 0);

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
