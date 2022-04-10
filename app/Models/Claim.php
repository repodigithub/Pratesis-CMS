<?php

namespace App\Models;

use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoDistributor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Claim extends Model
{
  const STATUS_DRAFT = 'draft';
  const STATUS_SUBMIT = 'submit';
  const STATUS_APPROVE = 'approve';
  const STATUS_REJECT = 'reject';

  const LAPORAN_PAYED = 'sudah_bayar';
  const LAPORAN_CAN_PAY = 'layak_bayar';

  protected $table = "claim";

  public $dates = ['approved_date'];

  public $fillable = [
    'kode_uli',
    'promo_distributor_id',
    'status',
    'amount',
    'laporan_tpr_barang',
    'laporan_tpr_uang',
    'faktur_pajak',
    'description',
    'alasan',
    'approved_date',
    'bukti_bayar'
  ];

  public $appends = ['status_claim'];

  public $hidden = ['status_claim'];

  public static function rules(Claim $claim = null)
  {
    $promo = PromoDistributor::find(request()->input('promo_distributor_id'));
    if (empty($promo)) {
      throw new BadRequestHttpException("promo_not_found");
    }
    $budget = $promo->budget;

    if ($promo->is_claimed) {
      throw new BadRequestHttpException("error_promo_is_claimed");
    }

    $rules = [
      'promo_distributor_id' => 'required|exists:promo_distributor,id',
      'amount' => ['nullable', 'numeric', 'max:' . $budget],
      'status' => ['required', Rule::in([self::STATUS_DRAFT, self::STATUS_SUBMIT])],
      'laporan_tpr_barang' => 'nullable',
      'laporan_tpr_uang' => 'nullable',
      'faktur_pajak' => 'nullable',
      'description' => 'nullable',
    ];
    return $rules;
  }

  public function getStatusClaimAttribute()
  {
    return !empty($this->bukti_bayar) ? self::LAPORAN_PAYED : self::LAPORAN_CAN_PAY;
  }

  public function promoDistributor()
  {
    return $this->belongsTo(PromoDistributor::class);
  }
}
