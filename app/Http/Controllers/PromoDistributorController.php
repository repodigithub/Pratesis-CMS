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
        $this->middleware("group:" . User::ROLE_DISTRIBUTOR, ['only' => ['updateStatus']]);
    }

    public function index($id = null, Request $req)
    {
        if (strpos($req->getPathInfo(), 'promo-distributor') !== false) {
            if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $data = auth()->user()->distributor->promos();
        } else {
            $data = $this->getModel(PromoArea::class, $id)->promoDistributors();
        }
        $pagination = $this->getPagination($req);
        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new PromoDistributor())->getTable() . '.' . $sort[0], $sort[1]);
        }
        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

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

    public function show($id = null, $dis = null, Request $req)
    {
        if (strpos($req->getPathInfo(), 'promo-distributor') !== false) {
            if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $data = $this->getModel(PromoDistributor::class, $id);
        } else {
            $data = $this->getModel(PromoArea::class, $id);
            $data = $this->getModel(PromoDistributor::class, $dis);
        }

        return $this->response($data->makeVisible(['statistics']));
    }

    public function update($id, $dis, Request $req)
    {
        $pa = $this->getModel(PromoArea::class, $id);
        $data = $this->getModel(PromoDistributor::class, $dis);
        $this->validate($req, PromoDistributor::rules($pa, $data));
        $data->update($req->all());
        $data = $this->getModel(PromoDistributor::class, $data->id);

        return $this->response($data);
    }

    public function updateStatus($id, Request $req)
    {
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
