<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Promo\Promo;
use App\Models\Promo\PromoDistributor;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:api");
    }

    public function promo(Request $req)
    {
        $pagination = $this->getPagination($req);

        switch (auth()->user()->kode_group) {
            case User::ROLE_GENERAL_ADMIN:
                $data = auth()->user()->area->promos();
                break;
            case User::ROLE_DISTRIBUTOR:
                throw new BadRequestHttpException('access_denied');
            default:
                $data = Promo::select([
                    'opso_id',
                    'nama_promo',
                    'status',
                    'start_date',
                    'end_date',
                ]);
                break;
        }

        $opso_from = $req->query('opso_from');
        $opso_to = $req->query('opso_to');

        if (!empty($opso_from) && !empty($opso_to)) $data->whereBetween('opso_id', [$opso_from, $opso_to]);
        elseif (!empty($opso_from)) $data->where('opso_id', '>=', $opso_from);
        elseif (!empty($opso_to)) $data->where('opso_id', '<=', $opso_to);

        $start_date_from = $req->query('start_date_from');
        $start_date_to = $req->query('start_date_to');

        if (!empty($start_date_from) && !empty($start_date_to)) $data->whereBetween('start_date', [$start_date_from, $start_date_to]);
        elseif (!empty($start_date_from)) $data->where('start_date', '>=', $start_date_from);
        elseif (!empty($start_date_to)) $data->where('start_date', '<=', $start_date_to);

        $end_date_from = $req->query('end_date_from');
        $end_date_to = $req->query('end_date_to');

        if (!empty($end_date_from) && !empty($end_date_to)) $data->whereBetween('end_date', [$end_date_from, $end_date_to]);
        elseif (!empty($end_date_from)) $data->where('end_date', '>=', $end_date_from);
        elseif (!empty($end_date_to)) $data->where('end_date', '<=', $end_date_to);

        if (!empty($req->query('status'))) {
            $data->where('status', $req->query('status'));
        }

        if (!empty($req->query('kode_area'))) {
            $data->whereHas('promoAreas', function ($query) use ($req) {
                $query->where('kode_area', $req->query('kode_area'));
            });
        }

        if (!empty($req->query('kode_brand'))) {
            $data->whereHas('promoProducts', function ($query) use ($req) {
                $query->where('kode_brand', $req->query('kode_brand'));
            });
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);
        $data->setCollection($data->getCollection()->map(function ($val) {
            $val->brands = $val->promoProducts()->get()->map(function ($v) {
                return $v->brand()->first()->only('kode_brand', 'nama_brand');
            });
            $val->promo_area = $val->promoAreas()->get()->pluck('kode_area');

            return $val->makeHidden(['document', 'thumbnail']);
        }));

        return $this->response($data);
    }

    public function claim(Request $req)
    {
        if (empty(auth()->user()->kode_area)) {
            throw new BadRequestHttpException('user_kode_area_empty');
        }

        $pagination = $this->getPagination($req);

        $data = Claim::where('status', Claim::STATUS_APPROVE)
            ->whereHas('promoDistributor', function ($q) use ($req) {
                $q->whereIn("kode_distributor", auth()->user()->area->distributors->pluck('kode_distributor'));
                if ($req->filled('kode_distributor')) {
                    $q->where('kode_distributor', $req->query('kode_distributor'));
                }
            });


        if ($req->filled('status_claim')) {
            switch ($req->query('status_claim')) {
                case Claim::LAPORAN_CAN_PAY:
                    $data->whereNull('bukti_bayar');
                    break;
                case Claim::LAPORAN_PAYED:
                    $data->whereNotNull('bukti_bayar');
                    break;
                case 'all':
                default:
                    # code...
                    break;
            }
            # code...
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);
        $data->setCollection($data->getCollection()->map(function ($val) {
            $promoDistributor = $val->promoDistributor()->first();
            $val->claim_amount = $promoDistributor->budget;
            $tipe_promo = $promoDistributor->promo->promoType->first();

            $val->ppn_percentage = number_format($tipe_promo->persentase_ppn);
            $val->ppn_amount = number_format($val->amount * $tipe_promo->persentase_ppn / 100, 2);
            $val->pph_percentage = number_format($tipe_promo->persentase_pph);
            $val->pph_amount = number_format($val->amount * $tipe_promo->persentase_pph / 100, 2);
            return $val->makeVisible('status_claim');
        }));

        return $this->response($data);
    }

    public function getAreaByHo(Request $req)
    {
        return $this->response('ok');
    }

    public function getBrandByHo(Request $req)
    {
        return $this->response('ok');
    }

    public function getReportPromoByHo(Request $req)
    {
        return $this->response('ok');
    }

    public function getReportAreaByHo(Request $req)
    {
        return $this->response('ok');
    }

    public function getReportBrandByHo(Request $req)
    {
        return $this->response('ok');
    }
}
