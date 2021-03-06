<?php

namespace App\Models\Promo;

use App\Models\Claim;
use App\Models\Spend;
use App\Models\TipePromo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Promo extends Model
{
  const STATUS_DRAFT = 'draft';
  const STATUS_NEED_APPROVAL = 'need_approval';
  const STATUS_APPROVE = 'approve';
  const STATUS_REJECT = 'reject';

  protected $table = "promo";

  public $dates = ['start_date', 'end_date'];

  public $fillable = [
    'opso_id',
    'nama_promo',
    'budget',
    'status',
    'start_date',
    'end_date',
    'claim',
    'kode_spend_type',
    'kode_budget_holder',
    'file',
  ];

  public $hidden = ['file', 'statistics', 'budget_product', 'budget_area', 'budget_claimed'];

  public $appends = ['document', 'thumbnail', 'statistics'];

  public function getThumbnailAttribute()
  {
    $thumbnail = $this->promoImages()->first();
    if (!empty($thumbnail)) {
      return $thumbnail->link;
    }
    return null;
  }

  public function getBudgetProductAttribute()
  {
    return (int) $this->promoProducts()->select(DB::raw('SUM(budget_brand)'))->getQuery()->first()->sum;
  }

  public function getBudgetAreaAttribute()
  {
    return (int) $this->promoAreas()->select(DB::raw('SUM(budget)'))->getQuery()->first()->sum;
  }

  public function getBudgetClaimedAttribute()
  {
    return (int) Claim::selectRaw('SUM(amount)')->whereIn('claim.promo_distributor_id', $this->promoDistributors()->pluck('promo_distributor.id'))->getQuery()->first()->sum;
  }

  public function getDocumentAttribute()
  {
    if (!empty($this->file)) {
      return url($this->file);
    }
    return '';
  }

  public function getStatisticsAttribute()
  {
    return [
      "budget" => $this->budget,
      "budget_update" => $this->budget_product,
      "budget_left" => $this->budget - $this->budget_product,
      "claim" => $this->budget_claimed,
      "outstanding_claim" => $this->budget - $this->budget_claimed,
      "budget_area" => $this->budget_area,
    ];
  }

  public function promoType()
  {
    return $this->belongsTo(Spend::class, 'kode_spend_type', 'kode_spend_type')
      ->first()->belongsToMany(
        TipePromo::class,
        'tipe_promo_spend_type',
        'kode_spend_type',
        'kode_kegiatan',
        'kode_spend_type',
        'kode_kegiatan'
      );
  }

  public function promoImages()
  {
    return $this->hasMany(PromoImage::class, 'opso_id', 'opso_id');
  }

  public function promoProducts()
  {
    return $this->hasMany(PromoBrand::class, 'opso_id', 'opso_id');
  }

  public function promoAreas()
  {
    return $this->hasMany(PromoArea::class, 'opso_id', 'opso_id');
  }

  public function promoDistributors()
  {
    return $this->hasManyThrough(PromoDistributor::class, PromoArea::class, 'opso_id', 'promo_area_id', 'opso_id', 'promo_area.id');
  }
}
