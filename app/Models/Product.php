<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  const FILE_PATH = "product";
  const FILE_NAME = "MSTPRODUCT";
  const WORKSHEET_NAME = "PRODUCTMST";
  const FIELD_NAME = ["Kode Produk", "Nama Produk", "Kode Sub Brand", "Kode Brand", "Kode Category", "Kode Divisi"];

  protected $table = "produk";

  public $fillable = ["kode_produk", "nama_produk", "kode_sub_brand", "kode_brand", "kode_category", "kode_divisi"];
}
