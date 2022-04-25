<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Brand;
use App\Models\Claim;
use App\Models\Divisi;
use App\Models\Product;
use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoDistributor;
use App\Models\Promo\PromoProduct;
use App\Models\Region;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getMiniData(Request $req)
    {
        $this->validate($req, [
            'level' => 'nullable|in:depot,distributor,ho'
        ]);

        $budget = '';
        $outstanding = '';
        $claim = '';
        $sisa = '';

        $sisa_promo = PromoDistributor::doesntHave('claim')
            ->whereHas('promoArea', function ($query) {
                $query->whereHas('promo', function ($q) {
                    $q->whereDate('start_date', '<=', date('Y-m-d'));
                    $q->whereDate('end_date', '>=', date('Y-m-d'));
                });
            });

        switch ($req->query('level')) {
            case 'depot':
                $kode_area = auth()->user()->kode_area;
                $promo_area = PromoArea::where('kode_area', $kode_area);
                $kode_distributors = PromoDistributor::whereHas('promoArea', function ($query) use ($kode_area) {
                    $query->where('kode_area', $kode_area);
                })->get()->pluck('kode_distributor');

                $budget = (int) $promo_area->selectRaw('SUM(budget)')->getQuery()->first()->sum;
                $claim = (int) Claim::whereHas('promoDistributor', function ($query) use ($kode_distributors) {
                    $query->whereIn('kode_distributor', $kode_distributors);
                })->selectRaw('SUM(amount)')->getQuery()->first()->sum;
                $sisa = (int) $sisa_promo->whereIn('kode_distributor', $kode_distributors)->count();

                break;
            case 'distributor':
                $kode_distributor = auth()->user()->kode_distributor;
                $promo_distributor = PromoDistributor::where('kode_distributor', $kode_distributor);

                $budget = (int) (clone $promo_distributor)->selectRaw('SUM(budget)')->getQuery()->first()->sum;
                $claim = (int) Claim::whereIn('claim.promo_distributor_id', (clone $promo_distributor)->get()->pluck('id'))->selectRaw('SUM(amount)')->getQuery()->first()->sum;
                $sisa = (int) $sisa_promo->where('kode_distributor', $kode_distributor)->count();
                break;
            case 'ho':
            default:
                $budget = (int) Promo::selectRaw('SUM(budget)')->getQuery()->first()->sum;
                $claim = (int) Claim::selectRaw('SUM(amount)')->getQuery()->first()->sum;
                $sisa = (int) $sisa_promo->count();
                break;
        }

        $outstanding = $budget - $claim;

        return $this->response([
            "budget" => $budget,
            "outstanding" => $outstanding,
            "claim" => $claim,
            "sisa" => $sisa,
        ]);;
    }

    public function getArea(Request $req)
    {
        $data = Area::select('*')->get()->makeHidden([
            'kode_region',
            'created_at',
            'updated_at',
            'nama_region'
        ]);
        return $this->response($data);;
    }

    public function getByDivisi(Request $req)
    {
        $data = Divisi::select('divisi.*')->selectRaw('SUM(promo_product.budget_produk) as budget')
            ->join('produk', 'divisi.kode_divisi', '=', 'produk.kode_divisi')
            ->join('promo_product', 'promo_product.kode_produk', '=', 'produk.kode_produk')
            ->groupBy('divisi.id')
            ->orderBy('budget', 'desc')
            ->limit(5)
            ->get();
        return $this->response($data);
    }

    public function getByBrand(Request $req)
    {
        $data = Brand::select('brand.*')->selectRaw('SUM(promo_product.budget_produk) as budget')
            ->join('produk', 'brand.kode_brand', '=', 'produk.kode_brand')
            ->join('promo_product', 'promo_product.kode_produk', '=', 'produk.kode_produk')
            ->groupBy('brand.id')
            ->orderBy('budget', 'desc')
            ->limit(5)
            ->get();
        return $this->response($data);
    }

    public function getByRegion(Request $req)
    {
        $data = Region::select('region.*')->selectRaw('SUM(promo_area.budget) as budget')
            ->join('area', 'region.kode_region', '=', 'area.kode_region')
            ->join('promo_area', 'promo_area.kode_area', '=', 'area.kode_area')
            ->groupBy('region.id')
            ->orderBy('budget', 'desc')
            ->limit(5)
            ->get();
        return $this->response($data);
    }

    public function getByArea(Request $req)
    {
        $data = Area::select('area.*')->selectRaw('SUM(promo_area.budget) as budget')
            ->join('promo_area', 'promo_area.kode_area', '=', 'area.kode_area')
            ->groupBy('area.id')
            ->orderBy('budget', 'desc')
            ->limit(5)
            ->get();
        return $this->response($data);
    }

    public function getTidakLayakBayar(Request $req)
    {
        $this->validate($req, [
            'level' => 'nullable|in:depot,distributor'
        ]);

        $pagination = $this->getPagination($req);

        $data = Claim::select("*")->whereNull('bukti_bayar')->where('status', Claim::STATUS_REJECT);

        switch ($req->query('level')) {
            case 'depot':
                break;
            case 'distributor':
            default:
                $kode_distributor = auth()->user()->kode_distributor;
                $data->whereIn('promo_distributor_id', PromoDistributor::where('kode_distributor', $kode_distributor)->pluck('id'));
                break;
        }

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new Claim())->getTable() . '.' . $sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        $data->setCollection($data->getCollection()->map(function ($val) {
            $promo = $val->promoDistributor;
            $tipe_promo = $promo->promo->promoType->first();

            $val->claim = $promo->budget;
            $val->jenis_kegiatan = !empty($tipe_promo->nama_kegiatan) ? $tipe_promo->nama_kegiatan : '';
            return $val->makeHidden(['promoDistributor']);
        }));

        return $this->response($data);
    }

    public function getMenungguPembayaran(Request $req)
    {
        $this->validate($req, [
            'level' => 'nullable|in:depot,distributor'
        ]);

        $pagination = $this->getPagination($req);

        $data = Claim::select("*")->whereNull('bukti_bayar')->where('status', Claim::STATUS_APPROVE);

        switch ($req->query('level')) {
            case 'depot':
                break;
            case 'distributor':
            default:
                $kode_distributor = auth()->user()->kode_distributor;
                $data->whereIn('promo_distributor_id', PromoDistributor::where('kode_distributor', $kode_distributor)->pluck('id'));
                break;
        }

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new Claim())->getTable() . '.' . $sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);


        $data->setCollection($data->getCollection()->map(function ($val) {
            $promo = $val->promoDistributor;
            $tipe_promo = $promo->promo->promoType->first();

            $val->claim = $promo->budget;
            $val->jenis_kegiatan = !empty($tipe_promo->nama_kegiatan) ? $tipe_promo->nama_kegiatan : '';
            return $val->makeVisible(['status_claim'])->makeHidden(['promoDistributor', 'status']);
        }));

        return $this->response($data);
    }
}
