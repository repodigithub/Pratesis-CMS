<?php

namespace App\Http\Controllers;

use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoAreaController extends Controller
{
    function __construct()
    {
        $this->middleware("auth:api");
    }

    public function index($id = null, Request $req)
    {
        if (strpos($req->getPathInfo(), 'promo-depot') !== false) {
            if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $data = auth()->user()->area->promos();
        } else {
            $data = $this->getModel(Promo::class, $id);
            $data = $data->promoAreas();
        }

        $pagination = $this->getPagination($req);

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new PromoArea())->getTable() . '.' . $sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        return $this->response($data);
    }

    public function create($id, Request $req)
    {
        $data = $this->getModel(Promo::class, $id);

        $this->validate($req, PromoArea::rules());

        // $req->merge(['status' => PromoArea::STATUS_NEW_PROMO]);
        $data = $data->promoAreas()->create($req->all());

        $data = $this->getModel(PromoArea::class, $data->id);

        return $this->response($data);
    }

    public function show($id = null, $area = null, Request $req)
    {
        if (strpos($req->getPathInfo(), 'promo-depot') !== false) {
            if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $data = $this->getModel(PromoArea::class, $id);
        } else {
            $data = $this->getModel(Promo::class, $id);
            $data = $this->getModel(PromoArea::class, $area);
        }

        return $this->response($data);
    }

    public function update($id, $area, Request $req)
    {
        $promo = $this->getModel(Promo::class, $id);

        $data = $this->getModel(PromoArea::class, $area);

        $this->validate($req, PromoArea::rules($promo->opso_id));

        $data->update($req->all());

        $data = $this->getModel(PromoArea::class, $data->id);

        return $this->response($data);
    }


    public function updateStatus($id, Request $req)
    {
        if (strpos($req->getPathInfo(), 'promo-depot') !== false) {
            if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $data = $this->getModel(PromoArea::class, $id);
        } else {
            if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
        }
        $data = $this->getModel(PromoArea::class, $id);

        $this->validate($req, [
            'status' => ['nullable', Rule::in([
                PromoArea::STATUS_NEW_PROMO,
                PromoArea::STATUS_NEED_APPROVAL,
                PromoArea::STATUS_APPROVE,
                PromoArea::STATUS_REJECT,
            ])]
        ]);

        $data->status = $req->input('status');
        $data->save();
        $data = $this->getModel(PromoArea::class, $id);

        return $this->response($data);
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
