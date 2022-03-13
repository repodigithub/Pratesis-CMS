<?php

namespace App\Models\Promo;

use App\Models\Area;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PromoArea extends Model
{
  protected $table = "promo_area";

  public $fillable = ['opso_id', 'kode_area', 'budget'];

  public $appends = ['nama_area', 'region', 'alamat', 'persentase'];

  public static function rules($opso_id = null)
  {
    $rules = [];
    $rules['kode_area'] = [
      'required',
      'exists:area,kode_area',
      Rule::unique('promo_area')->where(function ($query) use ($opso_id) {
        return $query->where('opso_id', '!=', $opso_id);
      })
    ];
    $rules['budget'] = ['required', 'numeric'];
    return $rules;
  }

  public function getNamaAreaAttribute()
  {
    return $this->area()->first()->nama_area;
  }

  public function getRegionAttribute()
  {
    return $this->area()->first()->region()->first()->nama_region;
  }

  public function getAlamatAttribute()
  {
    return $this->area()->first()->alamat_depo;
  }

  public function getPersentaseAttribute()
  {
    try {
      $budget = $this->promo()->first()->budget;
      return $this->budget / $budget * 100;
    } catch (\Throwable $th) {
      return 0;
    }
  }

  public function promo()
  {
    return $this->belongsTo(Promo::class, 'opso_id', 'opso_id');
  }

  public function area()
  {
    return $this->belongsTo(Area::class, 'kode_area', 'kode_area');
  }
}
