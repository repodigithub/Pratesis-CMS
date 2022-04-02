<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Promo\PromoDistributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ClaimController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:api");
    }

    public function index(Request $req)
    {
        $pagination = $this->getPagination($req);

        $data = Claim::whereHas('promoDistributor', function ($q) {
            $q->where("kode_distributor", auth()->user()->kode_distributor);
        });

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new Claim())->getTable() . '.' . $sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        return $this->response($data);
    }

    public function upload(Request $req)
    {
        $this->validate($req, ['file' => 'required|file']);
        $file = $req->file('file');
        $filename = $file->getClientOriginalName();
        $path = implode('/', ['claim', date('Ymd/His')]);
        $file->move(storage_path("app/public/" . $path), $filename);
        return $this->response(url("storage/$path/$filename"));
    }

    public function create(Request $req)
    {
        $this->validate($req, Claim::rules());

        $promo = $this->getModel(PromoDistributor::class, $req->promo_distributor_id);

        if ($promo->is_claimed) {
            throw new BadRequestHttpException("error_promo_is_claimed");
        }

        $claim = DB::transaction(function () use ($req, $promo) {
            return Claim::create([
                'promo_distributor_id' => $promo->id,
                'kode_uli' => $promo->kode_distributor . date('y') . str_pad(Claim::count() + 1, 4, 0, STR_PAD_LEFT),
                'status' => $req->input('status'),
                'amount' => $req->input('amount', 0),
                'laporan_tpr_barang' => $req->input('laporan_tpr_barang'),
                'laporan_tpr_uang' => $req->input('laporan_tpr_uang'),
                'faktur_pajak' => $req->input('faktur_pajak'),
                'description' => $req->input('description'),
            ]);
        });

        return $this->response($claim);
        // Kode distributor + tahun (2 angka belakang)+ 4 increment
        # code...
    }

    public function show($id, Request $req)
    {
        $include = null;
        if ($req->filled('include')) {
            $include = $req->query('include');
        }

        $data = $this->getModel(Claim::class, $id, $include)->makeVisible([
            'kode_distributor',
            'nama_distributor',
            'jenis_kegiatan',
            'ppn_amount',
            'pph_amount',
            'total_amount'
        ]);

        return $this->response($data);
    }
}
