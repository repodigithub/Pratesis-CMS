<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  const FILE_PATH = "category";
  const FILE_NAME = "MSTCATEGORY";
  const WORKSHEET_NAME = "CATEGORYMST";
  const FIELD_NAME = ["Kode Category", "Nama Category"];

  protected $table = "kategori";

  public $fillable = ["kode_kategori", "nama_kategori"];
}
