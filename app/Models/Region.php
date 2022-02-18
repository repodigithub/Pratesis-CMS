<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
  use HasFactory;
  const FILE_PATH = "region";
  const FILE_NAME = "MSTREGION";
  const WORKSHEET_NAME = "REGIONMST";
  const FIELD_NAME = ["Kode Region", "Nama Region"];

  protected $table = "region";

  public $fillable = ["kode_region", "nama_region"];
}
