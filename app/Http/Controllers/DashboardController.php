<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoDistributor;
use App\Models\Promo\PromoProduct;
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

    public function getByDivisi(Request $req)
    {
    }

    public function getByBrand(Request $req)
    {
    }

    public function getByRegion(Request $req)
    {
    }

    public function getByArea(Request $req)
    {
    }

    public function getTidakLayakBayar(Request $req)
    {
    }

    public function getMenungguPembayaran(Request $req)
    {
    }
}
