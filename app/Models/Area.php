<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
  const FILE_PATH = "area";
  const FILE_NAME = "MSTAREA";
  const WORKSHEET_NAME = "AREAMST";
  const FIELD_NAME = ["Kode Area", "Nama Area", "Alamat", "Titik Koordinat", "Kode Region"];

  protected $table = "area";

  public $fillable = ["kode_area", "nama_area", "alamat_depo", "titik_koordinat", "kode_region"];

  public $appends = ["nama_region"];

  public function region()
  {
    return $this->belongsTo(Region::class, 'kode_region', 'kode_region');
  }

  public function getNamaRegionAttribute()
  {
    return $this->region()->first()->nama_region;
  }
}
