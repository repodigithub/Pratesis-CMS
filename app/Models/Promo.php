<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Promo extends Model
{
  const FILE_PATH = "distributor";
  const FILE_NAME = "MSTDISTRIBUTOR";
  const WORKSHEET_NAME = "DISTRIBUTORMST";
  const FIELD_NAME = ["Kode Distributor", "Nama distributor", "Kode Distributor Group", "Kode Area", "Alamat", "Titik Koordinat", "Status"];

  const STATUS_ACTIVE = "aktif";
  const STATUS_NON_ACTIVE = "tidak-aktif";

  protected $table = "distributor";

  public $fillable = [
    "kode_distributor",
    "nama_distributor",
    "kode_distributor_group",
    "kode_area",
    "alamat",
    "titik_koordinat",
    "status_distributor",
  ];

  public $appends = ["nama_region"];

  public function distributorGroup()
  {
    return $this->belongsTo(DistributorGroup::class, "kode_distributor_group", "kode_distributor_group");
  }

  public function area()
  {
    return $this->belongsTo(Area::class, "kode_area", "kode_area");
  }

  public function region()
  {
    return $this->area()->first()->region();
  }

  public function getNamaRegionAttribute()
  {
    return $this->region()->first()->nama_region;
  }
}
