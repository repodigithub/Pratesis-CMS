<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
  use HasFactory;
  const FILE_PATH = "product";
  const FILE_NAME = "MSTPRODUCT";
  const WORKSHEET_NAME = "PRODUCTMST";
  const FIELD_NAME = ["Kode Produk", "Nama Produk", "Kode Sub Brand", "Kode Brand", "Kode Category", "Kode Divisi"];

  protected $table = "produk";

  public $fillable = ["kode_produk", "nama_produk", "kode_sub_brand", "kode_brand", "kode_kategori", "kode_divisi"];

  public function subBrand()
  {
    return $this->belongsTo(SubBrand::class, "kode_sub_brand", "kode_sub_brand");
  }

  public function brand()
  {
    return $this->belongsTo(Brand::class, "kode_brand", "kode_brand");
  }

  public function category()
  {
    return $this->belongsTo(Category::class, "kode_kategori", "kode_kategori");
  }

  public function divisi()
  {
    return $this->belongsTo(Divisi::class, "kode_divisi", "kode_divisi");
  }
}
