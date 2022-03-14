<?php

namespace App\Models\Promo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PromoImage extends Model
{
  protected $table = "promo_image";

  public $fillable = ['opso_id', 'file'];

  public $hidden = ['file'];

  public $appends = ['link'];

  public function getLinkAttribute()
  {
    return url($this->file);
  }

  public function promo()
  {
    return $this->belongsTo(Promo::class, 'opso_id', 'opso_id');
  }
}
