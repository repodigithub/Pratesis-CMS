<?php

namespace App\Models;

use App\Models\Promo\PromoDistributor;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
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

  public function promoDistributor()
  {
    return $this->belongsTo(PromoDistributor::class, 'promo_distributor_id');
  }
}
