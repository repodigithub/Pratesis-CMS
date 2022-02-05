<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
  const STATUS_ACTIVE = "aktif";
  const STATUS_NON_ACTIVE = "tidak-aktif";

  protected $table = "distributor";

  public $fillable = [
    "kode_distributor",
    "nama_distributor",
    "kode_distributor_group",
    "kode_area",
    "kode_region",
    "status_distributor",
  ];
}
