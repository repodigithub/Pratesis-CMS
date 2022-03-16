<?php

namespace App\Models\Promo;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PromoBrand extends Model
{
  const METHOD_MANUAL = 'manual';
  const METHOD_AUTO = 'otomatis';
  
  protected $table = "promo_brand";

  public $fillable = [
    'opso_id',
    'kode_brand',
    'budget_brand',
    'method'
  ];

  public $appends = ['nama_brand', 'produk_aktif', 'persentase'];

  public static function rules($opso_id = null)
  {
    $rules = [];
    $rules['kode_brand'] = [
      'required',
      'exists:brand,kode_brand',
      Rule::unique('promo_brand')->where(function ($query) use ($opso_id) {
        return $query->where('opso_id', '!=', $opso_id);
      })
    ];
    $rules['budget_brand'] = 'required|numeric';
    $rules['method'] = ['required', Rule::in([self::METHOD_AUTO, self::METHOD_MANUAL])];
    $rules['products'] = 'required|array';
    $rules['products.*.status'] = 'required|boolean';
    $rules['products.*.kode_produk'] = 'required|exists:produk,kode_produk|distinct';
    $rules['products.*.budget_produk'] = 'required|numeric';
    return $rules;
  }

  public function getProdukAktifAttribute()
  {
    return $this->products()->where('status', true)->count();
  }

  public function getPersentaseAttribute()
  {
    try {
      $budget = $this->promo()->first()->budget;
      return $this->budget_brand / $budget * 100;
    } catch (\Throwable $th) {
      return 0;
    }
  }

  public function getNamaBrandAttribute()
  {
    try {
      return $this->brand()->first()->nama_brand;
    } catch (\Throwable $th) {
      return '';
    }
  }

  public function brand()
  {
    return $this->belongsTo(Brand::class, 'kode_brand', 'kode_brand');
  }

  public function promo()
  {
    return $this->belongsTo(Promo::class, 'opso_id', 'opso_id');
  }

  public function products()
  {
    return $this->hasMany(PromoProduct::class, 'promo_brand_id');
  }
}
