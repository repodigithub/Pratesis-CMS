<?php

namespace App\Models\Promo;

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

  public $hidden = ['file', 'statistics', 'budget_product', 'budget_area'];

  public $appends = ['document', 'statistics'];

  public function getBudgetProductAttribute()
  {
    return (int) $this->promoProducts()->select(DB::raw('SUM(budget_brand)'))->getQuery()->first()->sum;
  }

  public function getBudgetAreaAttribute()
  {
    return (int) $this->promoAreas()->select(DB::raw('SUM(budget)'))->getQuery()->first()->sum;
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
      "claim" => 0,
      "outstanding_claim" => 0,
      "budget_area" => $this->budget_area,
    ];
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
}
