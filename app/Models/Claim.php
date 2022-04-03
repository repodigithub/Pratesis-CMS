<?php

namespace App\Models;

use App\Models\Promo\Promo;
use App\Models\Promo\PromoArea;
use App\Models\Promo\PromoDistributor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Claim extends Model
{
  const STATUS_DRAFT = 'draft';
  const STATUS_SUBMIT = 'submit';

  protected $table = "claim";

  public $fillable = [
    'kode_uli',
    'promo_distributor_id',
    'status',
    'amount',
    'laporan_tpr_barang',
    'laporan_tpr_uang',
    'faktur_pajak',
    'description',
  ];

  public static function rules(Claim $claim = null)
  {
    $rules = [
      'promo_distributor_id' => 'required|exists:promo_distributor,id',
      'amount' => 'nullable|numeric',
      'status' => ['required', Rule::in([self::STATUS_DRAFT, self::STATUS_SUBMIT])],
      'laporan_tpr_barang' => 'nullable|text',
      'laporan_tpr_uang' => 'nullable|text',
      'faktur_pajak' => 'nullable|text',
      'description' => 'nullable|text',
    ];
    return $rules;
  }

  public function promoDistributor()
  {
    return $this->belongsTo(PromoDistributor::class);
  }
}
