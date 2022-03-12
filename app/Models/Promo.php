<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Promo extends Model
{
  const STATUS_APPROVE = 'approve';
  const STATUS_NEED_APPROVAL = 'need_approval';
  const STATUS_REJECT = 'reject';
  const STATUS_DRAFT = 'draft';

  protected $table = "promo";

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

  public $appends = ['link'];

  public function getLinkAttribute()
  {
    return url($this->file);
  }

  public function promoArea()
  {
    return $this->hasMany(PromoArea::class, 'opso_id', 'opso_id');
  }
}
