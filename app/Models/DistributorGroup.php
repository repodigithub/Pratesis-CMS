<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorGroup extends Model
{
  const FILE_PATH = "region";
  const FILE_NAME = "MSTREGION";

  protected $table = "area";

  public $fillable = ["kode_area", "nama_area", "alamat_depo", "kode_region", "koordinat"];

  public function region()
  {
    return $this->belongsTo(Region::class, 'kode_region', 'kode_region');
  }
}
