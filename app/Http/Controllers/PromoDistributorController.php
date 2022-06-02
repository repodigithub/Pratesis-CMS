<?php

namespace App\Http\Controllers;

use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoDistributor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoDistributorController extends Controller
{
    function __construct()
    {
        $this->middleware("auth:api");
        // $this->middleware("group:" . User::ROLE_DISTRIBUTOR, ['only' => ['updateStatus']]);
    }

    public function index(Request $req, $id = null)
    {
        $this->validate($req, [
            'status' => ['nullable', Rule::in([
                PromoDistributor::STATUS_APPROVE,
                PromoDistributor::STATUS_CLAIM,
                PromoDistributor::STATUS_END,
            ])]
        ]);
        $is_from_distributor = strpos($req->getPathInfo(), 'promo-distributor') !== false;
        if ($is_from_distributor) {
            // if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $data = auth()->user()->distributor->promos();
        } else {
            $data = $this->getModel(PromoArea::class, $id)->promoDistributors();
            // $data->whereNotNull('status');
        }

        // Filter
        $data->whereHas("promoArea", function ($q) use ($req) {
            $q->whereHas("promo", function ($r) use ($req) {
                if ($req->filled("opso_id")) {
                    $r->where("opso_id", "ILIKE", "%{$req->query("opso_id")}%");
                }
                if ($req->filled("nama")) {
                    $r->where("nama_promo", "ILIKE", "%{$req->query("nama")}%");
                }
                if ($req->filled("start_date")) {
                    $r->whereDate("start_date", '>=', date('Y-m-d', strtotime($req->query("start_date"))));
                }
                if ($req->filled("end_date")) {
                    $r->whereDate("end_date", '<=', date('Y-m-d', strtotime($req->query("end_date"))));
                }
                if ($req->filled("kode_spend_type")) {
                    $r->where("kode_spend_type", "{$req->query("kode_spend_type")}");
                }
            });
        });

        if ($req->filled("status")) {
            $data->whereIn("status", explode(',', $req->query("status")));
        }

        $pagination = $this->getPagination($req);
        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new PromoDistributor())->getTable() . '.' . $sort[0], $sort[1]);
        }
        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        if ($is_from_distributor) {
            $data->setCollection($data->getCollection()->makeHidden([
                "nama_distributor", "distributor_group",
            ]));
        } else {
            $data->setCollection($data->getCollection()->makeHidden([
                "opso_id", "nama_promo", "start_date", "end_date", "kode_spend_type",
            ]));
        }

        return $this->response($data);
    }

    public function create($id, Request $req)
    {
        $pa = $this->getModel(PromoArea::class, $id);
        $this->validate($req, PromoDistributor::rules($pa));
        // $req->merge(['status' => PromoArea::STATUS_NEW_PROMO]);
        $data = $pa->promoDistributors()->create($req->all());
        $data = $this->getModel(PromoDistributor::class, $data->id);

        return $this->response($data);
    }

    public function show(Request $req, $id = null, $dis = null)
    {
        if (strpos($req->getPathInfo(), 'promo-distributor') !== false) {
            // if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            if (!auth()->user()->distributor->promos()->where('id', $id)->count()) throw new NotFoundHttpException(PromoDistributor::class . " not found.");
            $data = $this->getModel(PromoDistributor::class, $id);
        } else {
            $data = $this->getModel(PromoArea::class, $id);
            $data = $this->getModel(PromoDistributor::class, $dis);
        }

        return $this->response($data->makeVisible(['statistics']));
    }

    public function update($id, $dis, Request $req)
    {
        if (!auth()->user()->area->promos()->where('id', $id)->count()) throw new NotFoundHttpException(PromoArea::class . " not found.");
        $pa = $this->getModel(PromoArea::class, $id);
        $data = $this->getModel(PromoDistributor::class, $dis);
        $this->validate($req, PromoDistributor::rules($pa, $data));
        $data->update($req->all());
        $data = $this->getModel(PromoDistributor::class, $data->id);

        return $this->response($data);
    }

    public function updateStatus($id, Request $req)
    {
        if (!auth()->user()->distributor->promos()->where('id', $id)->count()) throw new NotFoundHttpException(PromoDistributor::class . " not found.");
        $data = $this->getModel(PromoDistributor::class, $id);

        $this->validate($req, [
            'status' => ['nullable', Rule::in([
                PromoDistributor::STATUS_APPROVE,
                PromoDistributor::STATUS_CLAIM,
                PromoDistributor::STATUS_END
            ])]
        ]);

        $data->status = $req->input('status');
        $data->save();
        $data = $this->getModel(PromoDistributor::class, $id);

        return $this->response($data);
    }

    public function delete($id, $dis, Request $req)
    {
        $data = $this->getModel(PromoArea::class, $id);
        $data = $this->getModel(PromoDistributor::class, $dis);
        $data->delete();

        return $this->response();
    }

    public function deleteBatch($id, Request $req)
    {
        $this->getModel(PromoArea::class, $id);

        $this->validate($req, [
            'ids' => 'required|array',
            'ids.*' => 'required|distinct|exists:promo_distributor,id'
        ]);

        $count = PromoDistributor::whereIn('id', $req->input('ids'))->delete();

        return $this->response($count);
    }
}
