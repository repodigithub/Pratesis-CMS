<?php

namespace App\Http\Controllers;

use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use Illuminate\Http\Request;

class PromoAreaController extends Controller
{
    function __construct()
    {
        $this->middleware("auth:api");
    }

    public function index($id, Request $req)
    {
        $data = $this->getModel(Promo::class, $id);
        $pagination = $this->getPagination($req);

        $data = $data->promoAreas();

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

        $data = $data->promoAreas()->create($req->all());

        $data = $this->getModel(PromoArea::class, $data->id);

        return $this->response($data);
    }

    public function show($id, $area)
    {
        $data = $this->getModel(Promo::class, $id);
        $data = $this->getModel(PromoArea::class, $area);

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
