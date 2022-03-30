<?php

namespace App\Http\Controllers;

use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoDistributor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoAreaController extends Controller
{
    function __construct()
    {
        $this->middleware("auth:api");
        // $this->middleware("group:" . User::ROLE_DISTRIBUTOR, ['only' => ['updateStatus']]);
    }

    public function index($id = null, Request $req)
    {
        $this->validate($req, [
            'status' => ['nullable', Rule::in([
                PromoArea::STATUS_APPROVE,
                PromoArea::STATUS_NEED_APPROVAL,
                PromoArea::STATUS_NEW_PROMO,
                PromoArea::STATUS_REJECT
            ])]
        ]);
        $is_from_depot = strpos($req->getPathInfo(), 'promo-depot') !== false;
        if ($is_from_depot) {
            // if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $data = auth()->user()->area->promos();
        } else {
            $data = $this->getModel(Promo::class, $id);
            $data = $data->promoAreas();
        }

        // Fillter
        $data->whereHas("promo", function ($q) use ($req) {
            if ($req->filled("nama")) {
                $q->where("nama_promo", "ILIKE", "%{$req->query("nama")}%");
            }
            if ($req->filled("opso_id")) {
                $q->where("opso_id", "ILIKE", "%{$req->query("opso_id")}%");
            }

            if ($req->filled("start_date")) {
                $q->whereDate("start_date", date('Y-m-d', strtotime($req->query("start_date"))));
            }

            if ($req->filled("end_date")) {
                $q->whereDate("end_date", date('Y-m-d', strtotime($req->query("end_date"))));
            }

            if ($req->filled("kode_spend_type")) {
                $q->where("kode_spend_type", $req->query("kode_spend_type"));
            }
        });

        if ($req->filled("kode_area")) {
            $data->where('kode_area', $req->query("kode_area"));
        }

        if ($req->filled("status")) {
            $data->where("status", $req->query("status"));
        }

        $pagination = $this->getPagination($req);

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new PromoArea())->getTable() . '.' . $sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        if ($is_from_depot) {
            $data->setCollection($data->getCollection()->makeHidden([
                'nama_area', 'region', 'alamat',
            ]));
        } else {
            $data->setCollection($data->getCollection()->makeHidden([
                "nama_promo", "start_date", "end_date", "kode_spend_type",
            ]));
        }

        return $this->response($data);
    }

    public function create($id, Request $req)
    {
        $promo = $this->getModel(Promo::class, $id);

        $this->validate($req, PromoArea::rules($promo));

        // $req->merge(['status' => PromoArea::STATUS_NEW_PROMO]);
        $data = $promo->promoAreas()->create($req->all());

        $data = $this->getModel(PromoArea::class, $data->id);

        return $this->response($data);
    }

    public function show($id = null, $area = null, Request $req)
    {
        if (strpos($req->getPathInfo(), 'promo-depot') !== false) {
            if (!auth()->user()->area->promos()->where('id', $id)->count()) throw new NotFoundHttpException(PromoArea::class . " not found.");
            $data = $this->getModel(PromoArea::class, $id);
        } else {
            $data = $this->getModel(Promo::class, $id);
            $data = $this->getModel(PromoArea::class, $area);
        }

        return $this->response($data->makeVisible(['statistics']));
    }

    public function update($id, $area, Request $req)
    {
        $promo = $this->getModel(Promo::class, $id);

        $data = $this->getModel(PromoArea::class, $area);

        $this->validate($req, PromoArea::rules($promo, $data));

        $data->update($req->all());

        $data = $this->getModel(PromoArea::class, $data->id);

        return $this->response($data);
    }


    public function updateStatus($id, Request $req)
    {
        $data = $this->getModel(PromoArea::class, $id);

        $this->validate($req, [
            'status' => ['nullable', Rule::in([
                PromoArea::STATUS_NEW_PROMO,
                PromoArea::STATUS_NEED_APPROVAL,
                PromoArea::STATUS_APPROVE,
                PromoArea::STATUS_REJECT,
            ])]
        ]);

        DB::transaction(function () use ($data, $req) {
            $data->status = $req->input('status');
            $data->save();

            if ($req->input('status') == PromoArea::STATUS_APPROVE) {
                $data->promoDistributors()->update([
                    'status' => PromoDistributor::STATUS_APPROVE
                ]);
            }
        });
        $data = $this->getModel(PromoArea::class, $id);

        return $this->response($data);
    }

    public function status(Request $req)
    {
        $this->validate($req, [
            'ids' => 'required|array',
            'ids.*' => 'required|distinct|exists:promo_area,id',
            'status' => ['nullable', Rule::in([
                PromoArea::STATUS_APPROVE,
                PromoArea::STATUS_NEED_APPROVAL,
                PromoArea::STATUS_NEW_PROMO,
                PromoArea::STATUS_REJECT
            ])]
        ]);

        $count = DB::transaction(function () use ($req) {
            if ($req->input('status') == PromoArea::STATUS_APPROVE) {
                PromoDistributor::whereHas("promoArea", function ($q) use ($req) {
                    $q->whereIn('id', $req->input("ids"));
                })->update(['status' => PromoDistributor::STATUS_APPROVE]);
            }

            return PromoArea::whereIn('id', $req->input('ids'))->update([
                'status' => $req->input('status')
            ]);
        });

        return $this->response($count);
    }

    public function delete($id, $area, Request $req)
    {
        $data = $this->getModel(Promo::class, $id);

        $data = $this->getModel(PromoArea::class, $area);

        $data->delete();

        return $this->response();
    }


    public function deleteBatch($id, Request $req)
    {
        $data = $this->getModel(Promo::class, $id);

        $this->validate($req, [
            'ids' => 'required|array',
            'ids.*' => 'required|distinct|exists:promo_area,id'
        ]);

        $count = PromoArea::whereIn('id', $req->input('ids'))->delete();

        return $this->response($count);
    }
}
