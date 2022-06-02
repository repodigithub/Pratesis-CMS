<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
  const FILE_PATH = "brand";
  const FILE_NAME = "MSTBRAND";
  const WORKSHEET_NAME = "BRANDMST";
  const FIELD_NAME = ["Kode Brand", "Nama Brand",];

  protected $table = "brand";

  public $fillable = ["kode_brand", "nama_brand"];

  public function products()
  {
    return $this->hasMany(Product::class, 'kode_brand', 'kode_brand');
  }
}
