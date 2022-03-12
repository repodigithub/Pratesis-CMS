<?php

namespace App\Models\Promo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PromoArea extends Model
{
  protected $table = "promo_area";

  public $fillable = [];

  public function promo()
  {
    return $this->belongsTo(Promo::class, 'opso_id', 'opso_id');
  }
}
