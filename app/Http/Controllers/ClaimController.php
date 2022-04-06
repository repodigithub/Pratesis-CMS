<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Promo\PromoDistributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

        $data = Claim::whereHas('promoDistributor', function ($q) use ($req) {
            switch ($req->query('level')) {
                case 'depot':
                    $q->whereIn("kode_distributor", auth()->user()->area->distributors->pluck('kode_distributor'));
                    break;
                case 'ho':
                    break;
                default:
                    $q->where("kode_distributor", auth()->user()->kode_distributor);
                    break;
            }
        });

        $data->whereIn('status', [Claim::STATUS_DRAFT, Claim::STATUS_SUBMIT, Claim::STATUS_REJECT]);

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new Claim())->getTable() . '.' . $sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        $data->setCollection($data->getCollection()->map(function ($val) {
            $promo = $val->promoDistributor;
            $distributor = $promo->distributor;
            $tipe_promo = $promo->promo->promoType->first();

            $val->kode_distributor = !empty($distributor->kode_distributor) ? $distributor->kode_distributor : '';
            $val->nama_distributor = !empty($distributor->nama_distributor) ? $distributor->nama_distributor : '';
            $val->jenis_kegiatan = !empty($tipe_promo->nama_kegiatan) ? $tipe_promo->nama_kegiatan : '';
            $val->claim = $promo->budget;
            return $val->makeHidden('promoDistributor');
        }));

        return $this->response($data);
    }

    public function indexLaporan(Request $req)
    {
        $pagination = $this->getPagination($req);

        $data = Claim::whereHas('promoDistributor', function ($q) use ($req) {
            switch ($req->query('level')) {
                case 'depot':
                    $q->whereIn("kode_distributor", auth()->user()->area->distributors->pluck('kode_distributor'));
                    break;
                case 'ho':
                    break;
                default:
                    $q->where("kode_distributor", auth()->user()->kode_distributor);
                    break;
            }
        });

        $data->whereIn('status', [Claim::STATUS_APPROVE]);

        if (!empty($pagination->sort)) {
            $sort = $pagination->sort;
            $data->orderBy((new Claim())->getTable() . '.' . $sort[0], $sort[1]);
        }

        $data = $data->paginate($pagination->limit, ["*"], "page", $pagination->page);

        $data->setCollection($data->getCollection()->map(function ($val) {
            $promo = $val->promoDistributor;
            $tipe_promo = $promo->promo->promoType->first();

            $val->ppn_amount = $val->amount * $tipe_promo->persentase_ppn / 100;
            $val->pph_amount = $val->amount * $tipe_promo->persentase_pph / 100;
            $val->claim = $promo->budget;
            return $val->makeVisible(['status_claim'])->makeHidden(['promoDistributor', 'status']);
        }));

        return $this->response($data);
    }

    public function upload(Request $req)
    {
        $this->validate($req, ['file' => 'required|file']);
        $file = $req->file('file');
        $filename = str_replace(" ", "_", $file->getClientOriginalName());
        $path = implode('/', ['claim', date('Ymd/His')]);
        $file->move(storage_path("app/public/" . $path), $filename);
        return $this->response(url("storage/$path/$filename"));
    }

    public function uploadLaporan(Request $req)
    {
        $this->validate($req, ['file' => 'required|file']);
        $file = $req->file('file');
        $filename = str_replace(" ", "_", $file->getClientOriginalName());
        $path = implode('/', ['laporan-claim', date('Ymd/His')]);
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
            return Claim::create(
                array_merge(
                    $req->only([
                        'amount',
                        'status',
                        'laporan_tpr_barang',
                        'laporan_tpr_uang',
                        'faktur_pajak',
                        'description',
                    ]),
                    [
                        'promo_distributor_id' => $promo->id,
                        'kode_uli' => $promo->kode_distributor . date('y') . str_pad(Claim::count() + 1, 4, 0, STR_PAD_LEFT),
                    ]
                )
            );
        });

        return $this->response($claim);
        // Kode distributor + tahun (2 angka belakang)+ 4 increment
        # code...
    }

    public function show($id, Request $req)
    {
        $data = $this->getDetail($id);
        return $this->response($data);
    }

    public function update($id, Request $req)
    {
        $data = $this->getModel(Claim::class, $id);

        $this->validate($req, Claim::rules($data));

        $data->update($req->only([
            'amount',
            'status',
            'laporan_tpr_barang',
            'laporan_tpr_uang',
            'faktur_pajak',
            'description',
        ]));
        $data = $this->getModel(Claim::class, $id);

        return $this->response($data);
    }

    public function updateStatus($id, Request $req)
    {
        $data = $this->getModel(Claim::class, $id);

        $this->validate($req, [
            'status' => ['required', Rule::in([Claim::STATUS_APPROVE, Claim::STATUS_REJECT])],
        ]);

        $update = $req->only(['status', 'alasan']);
        if ($update['status'] == Claim::STATUS_APPROVE) {
            $update['approved_date'] = time();
        }
        $data->update($update);
        $data = $this->getModel(Claim::class, $id);

        return $this->response($data);
    }

    public function updateLaporan($id, Request $req)
    {
        $data = $this->getModel(Claim::class, $id);

        if ($data->status != Claim::STATUS_APPROVE) {
            throw new BadRequestHttpException("error_claim_not_approved");
        }

        $this->validate($req, [
            "bukti_bayar" => "required|url"
        ]);

        $data->update($req->only('bukti_bayar'));

        return $this->response($data->makeHidden('status')->makeVisible('status_claim'));
    }

    public function delete($id)
    {
        $data = $this->getModel(Claim::class, $id);
        $data->delete();
        return $this->response();
    }

    private function getDetail($id)
    {
        $data = $this->getModel(Claim::class, $id);

        $distributor = $data->promoDistributor->distributor;
        $promo = $data->promoDistributor->promo;
        $tipe_promo = $promo->promoType->first();

        $data->opso_id = $promo->opso_id;
        $data->nama_promo = $promo->nama_promo;
        $data->start_date = $promo->start_date;
        $data->end_date = $promo->end_date;

        $data->kode_distributor = $distributor->kode_distributor;
        $data->nama_distributor = $distributor->nama_distributor;
        $data->jenis_kegiatan = $tipe_promo->nama_kegiatan;
        $data->ppn_amount = $data->amount * $tipe_promo->persentase_ppn / 100;
        $data->pph_amount = $data->amount * $tipe_promo->persentase_pph / 100;
        $data->total_amount = $data->amount + $data->ppn_amount + $data->pph_amount;
        return $data->makeHidden('promoDistributor');
    }
}
