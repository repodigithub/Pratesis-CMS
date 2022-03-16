<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Master\MasterDataController;
use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoImageController extends MasterDataController
{
    public function __construct()
    {
        $this->model = PromoImage::class;
        $this->middleware("auth:api");
    }

    public function delete($id, $image)
    {
        $data = $this->getModel(Promo::class, $id);
        $data = $this->getModel(PromoImage::class, $image);
        $data->delete();
        try {
            $this->afterDelete($data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
        return $this->response();
    }

    protected function onFilter(Builder $query, Request $req)
    {
        if (strpos($req->getPathInfo(), 'promo-depot') !== false) {
            if (!auth()->user()->hasRole(User::ROLE_DISTRIBUTOR)) throw new NotFoundHttpException("path_not_found");
            $promo = $this->getModel(PromoArea::class, $req->route('id'))->promo;
        } else {
            $promo = $this->getModel(Promo::class, $req->route('id'));
        }
        $query = $promo->promoImages();

        return $query;
    }

    protected function rules($data = null)
    {
        $rules = [];
        $rules['file'] = 'required|file|image|mimes:jpg,png,jpeg,gif,svg';
        return $rules;
    }

    protected function beforeUpdateOrCreate(Request $req)
    {
        $data = $req->all();

        $id = $req->route('id');
        $promo = $this->getModel(Promo::class, $id);
        $data['opso_id'] = $promo->opso_id;

        $file = $req->file('file');
        $file_name = $file->getClientOriginalName();
        $file_path = implode("/", ['promo/image', date('Ymd/His')]);
        $file->move(storage_path('/app/public/' . $file_path), $file_name);

        $data['file'] = "/storage/${file_path}/{$file_name}";

        return new Request($data);
    }

    protected function afterDelete($data)
    {
        File::delete(storage_path(str_replace('storage', 'app/public', $data->file)));
    }
}
