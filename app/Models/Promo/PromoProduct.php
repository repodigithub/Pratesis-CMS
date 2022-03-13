<?php

namespace App\Models\Promo;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class PromoProduct extends Model
{
  protected $table = "promo_product";

  public $fillable = ['status', 'kode_produk', 'budget_produk'];

  public $appends = ['nama_produk', 'persentase'];

  public function getNamaProdukAttribute()
  {
    return $this->product()->first()->nama_produk;
  }

  public function getPersentaseAttribute()
  {
    $budget_brand = $this->promoBrand()->first()->budget_brand;

    return $this->budget_produk / $budget_brand * 100;
  }

  public function product()
  {
    return $this->belongsTo(Product::class, 'kode_produk', 'kode_produk');
  }

  public function promo()
  {
    return $this->promoBrand()->first()->promo();
  }

  public function promoBrand()
  {
    return $this->belongsTo(PromoBrand::class, 'promo_brand_id');
  }
}
