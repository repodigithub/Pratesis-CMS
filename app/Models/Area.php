<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
  const FILE_PATH = "area";
  const FILE_NAME = "MSTAREA";
  const WORKSHEET_NAME = "AREAMST";

  protected $table = "area";

  public $fillable = ["kode_area", "nama_area", "alamat_depo", "kode_region", "koordinat"];

  public function region()
  {
    return $this->belongsTo(Region::class, 'kode_region', 'kode_region');
  }
}
