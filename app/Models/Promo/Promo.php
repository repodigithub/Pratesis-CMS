<?php

namespace App\Models\Promo;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
  const STATUS_APPROVE = 'approve';
  const STATUS_NEED_APPROVAL = 'need_approval';
  const STATUS_REJECT = 'reject';
  const STATUS_DRAFT = 'draft';

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

  public $hidden = ['file'];

  public $appends = ['document'];

  public function getDocumentAttribute()
  {
    return url($this->file);
  }

  public function getStatisticsAttribute()
  {
    return [
      "budget" => $this->budget,
      "budget_update" => 0,
      "budget_left" => 0,
      "claim" => 0,
      "outstanding_claim" => 0,
      "budget_area" => 0,
    ];
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
