<?php

namespace App\Http\Controllers;

use App\Models\Promo\Promo;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:api");
    }

    private function promo(Request $req)
    {
        $pagination = $this->getPagination($req);
        $data = Promo::select([
            'opso_id',
            'nama_promo',
            'status',
            'start_date',
            'end_date',
        ]);

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
