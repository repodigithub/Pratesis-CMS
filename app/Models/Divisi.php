<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
  const FILE_PATH = "divisi";
  const FILE_NAME = "MSTDIVISI";
  const WORKSHEET_NAME = "DIVISIMST";
  const FIELD_NAME = ["Kode Divisi", "Nama Divisi"];

  protected $table = "divisi";

  public $fillable = ["kode_divisi", "nama_divisi"];
}
