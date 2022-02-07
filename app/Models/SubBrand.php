<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class SubBrand extends Model
{
  const FILE_PATH = "sub-brand";
  const FILE_NAME = "MSTSUBBRAND";
  const WORKSHEET_NAME = "SUBBRANDMST";
  const FIELD_NAME = ["Kode Sub Brand", "Nama Sub Brand"];

  protected $table = "sub_brand";

  public $fillable = ["kode_sub_brand", "nama_sub_brand"];
}
