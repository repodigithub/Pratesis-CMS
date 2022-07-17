<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Distributor;
use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoDistributor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    break;
            }
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);
        $data->setCollection($data->getCollection()->map(function ($val) {
            $promo = $val->promoDistributor()->first();
            $tipe_promo = $promo->promo->promoType->first();

            $ppn_percentage = number_format($tipe_promo->persentase_ppn);
            $ppn_amount = number_format($val->amount * $tipe_promo->persentase_ppn / 100, 2);
            $pph_percentage = number_format($tipe_promo->persentase_pph);
            $pph_amount = number_format($val->amount * $tipe_promo->persentase_pph / 100, 2);

            return [
                'id' => $val->id,
                'kode_uli' => $val->kode_uli,
                'kode_distributor' => $promo->kode_distributor,
                'nama_distributor' => $promo->nama_distributor,
                'description' => $val->description,
                'created_at' => $val->created_at,
                'approved_at' => $val->approved_date,
                'claim_amount' => $promo->budget,
                'ppn_percentage' => $ppn_percentage,
                'ppn_amount' => $ppn_amount,
                'pph_percentage' => $pph_percentage,
                'pph_amount' => $pph_amount,
                'payed_amount' => $val->amount,
                'status_claim' => $val->status_claim,
            ];
        }));

        return $this->response($data);
    }

    public function listClaim(Request $req)
    {
        if (empty(auth()->user()->kode_area)) {
            throw new BadRequestHttpException('user_kode_area_empty');
        }

        $pagination = $this->getPagination($req);

        $data = Claim::select('*')
            ->whereHas('promoDistributor', function ($q) use ($req) {
                $q->whereIn("kode_distributor", auth()->user()->area->distributors->pluck('kode_distributor'));

                // query kode distributor
                if ($req->filled('kode_distributor')) {
                    $q->where('kode_distributor', $req->query('kode_distributor'));
                }
            });

        // query opso id
        $data->whereHas('promoDistributor', function (Builder $query) use ($req) {
            $query->whereHas('promoArea', function (Builder $query) use ($req) {
                $query->whereHas('promo', function (Builder $query) use ($req) {
                    $opso_from = $req->query('opso_from');
                    $opso_to = $req->query('opso_to');

                    if (!empty($opso_from) && !empty($opso_to)) $query->whereBetween('opso_id', [$opso_from, $opso_to]);
                    elseif (!empty($opso_from)) $query->where('opso_id', '>=', $opso_from);
                    elseif (!empty($opso_to)) $query->where('opso_id', '<=', $opso_to);
                });
            });
        });

        // query claim created
        $claim_date_from = $req->query('claim_date_from');
        $claim_date_to = $req->query('claim_date_to');

        if (!empty($claim_date_from) && !empty($claim_date_to)) $data->whereBetween('created_at', [$claim_date_from, $claim_date_to]);
        elseif (!empty($claim_date_from)) $data->where('created_at', '>=', $claim_date_from);
        elseif (!empty($claim_date_to)) $data->where('created_at', '<=', $claim_date_to);

        // query status claim
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
                    break;
            }
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);
        $data->setCollection($data->getCollection()->map(function ($val) {
            $promo = $val->promoDistributor()->first();
            return [
                'id' => $val->id,
                'opso_id' => $promo->opso_id,
                'kode_distributor' => $promo->kode_distributor,
                'nama_distributor' => $promo->nama_distributor,
                'kode_uli' => $val->kode_uli,
                'status_claim' => $val->status_claim,
                'created_at' => $val->created_at,
            ];
        }));

        return $this->response($data);
    }

    public function listOpso(Request $req)
    {
        if (empty(auth()->user()->kode_area)) {
            throw new BadRequestHttpException('user_kode_area_empty');
        }

        $pagination = $this->getPagination($req, ['total_opso', 'desc']);

        $table = auth()->user()->area->distributors()->getQuery();
        $table->select([
            'distributor.id',
            'distributor.kode_distributor',
            'distributor.nama_distributor',
        ]);

        if ($req->filled('kode_distributor')) {
            $table->where('distributor.kode_distributor', $req->query('kode_distributor'));
        }

        $claim_submit = Claim::where('status', Claim::STATUS_SUBMIT);
        $claim_approve = Claim::where('status', Claim::STATUS_APPROVE);
        $claim_reject = Claim::where('status', Claim::STATUS_REJECT);
        $claim_layak_bayar = (clone $claim_approve)->whereNull('bukti_bayar');
        $claim_sudah_bayar = (clone $claim_approve)->whereNotNull('bukti_bayar');

        $table->selectRaw('COUNT(promo_distributor.id) as total_opso');
        $table->selectRaw('COUNT(claim_submit.id) as total_claim_submit');
        $table->selectRaw('COUNT(claim_approve.id) as total_claim_approve');
        $table->selectRaw('COUNT(claim_reject.id) as total_claim_reject');
        $table->selectRaw('COUNT(claim_layak_bayar.id) as total_claim_layak_bayar');
        $table->selectRaw('COUNT(claim_sudah_bayar.id) as total_claim_sudah_bayar');
        $table->groupBy('distributor.id');

        $table->leftJoin('promo_distributor', 'promo_distributor.kode_distributor', 'distributor.kode_distributor');
        $table->leftJoinSub($claim_submit, 'claim_submit', function ($join) {
            $join->on('claim_submit.promo_distributor_id', '=', 'promo_distributor.id');
        });
        $table->leftJoinSub($claim_approve, 'claim_approve', function ($join) {
            $join->on('claim_approve.promo_distributor_id', '=', 'promo_distributor.id');
        });
        $table->leftJoinSub($claim_reject, 'claim_reject', function ($join) {
            $join->on('claim_reject.promo_distributor_id', '=', 'promo_distributor.id');
        });
        $table->leftJoinSub($claim_layak_bayar, 'claim_layak_bayar', function ($join) {
            $join->on('claim_layak_bayar.promo_distributor_id', '=', 'promo_distributor.id');
        });
        $table->leftJoinSub($claim_sudah_bayar, 'claim_sudah_bayar', function ($join) {
            $join->on('claim_sudah_bayar.promo_distributor_id', '=', 'promo_distributor.id');
        });

        $data = DB::table(DB::raw("({$table->toSql()}) as sub"))
            ->mergeBindings($table->getQuery());

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy($sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        return $this->response($data);
    }

    public function listPromo(Request $req)
    {
        if (empty(auth()->user()->kode_area)) {
            throw new BadRequestHttpException('user_kode_area_empty');
        }

        $pagination = $this->getPagination($req, ['total_opso', 'desc']);

        $data = auth()->user()->area->promos();

        $data->whereHas('promo', function (Builder $query) use ($req) {
            $opso_from = $req->query('opso_from');
            $opso_to = $req->query('opso_to');

            if (!empty($opso_from) && !empty($opso_to)) $query->whereBetween('opso_id', [$opso_from, $opso_to]);
            elseif (!empty($opso_from)) $query->where('opso_id', '>=', $opso_from);
            elseif (!empty($opso_to)) $query->where('opso_id', '<=', $opso_to);

            $start_date_from = $req->query('start_date_from');
            $start_date_to = $req->query('start_date_to');

            if (!empty($start_date_from) && !empty($start_date_to)) $query->whereBetween('start_date', [$start_date_from, $start_date_to]);
            elseif (!empty($start_date_from)) $query->where('start_date', '>=', $start_date_from);
            elseif (!empty($start_date_to)) $query->where('start_date', '<=', $start_date_to);

            $end_date_from = $req->query('end_date_from');
            $end_date_to = $req->query('end_date_to');

            if (!empty($end_date_from) && !empty($end_date_to)) $query->whereBetween('end_date', [$end_date_from, $end_date_to]);
            elseif (!empty($end_date_from)) $query->where('end_date', '>=', $end_date_from);
            elseif (!empty($end_date_to)) $query->where('end_date', '<=', $end_date_to);

            if (!empty($req->query('status'))) {
                $query->where('status', $req->query('status'));
            }
        });

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);
        $data->setCollection($data->getCollection()->map(function ($val) {
            $promo = $val->promo()->first();
            return [
                'id' => $val->id,
                'opso_id' => $promo->opso_id,
                'nama_promo' => $promo->nama_promo,
                'start_date' => $promo->start_date,
                'end_date' => $promo->end_date,
                'status' => $promo->status,
            ];
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
