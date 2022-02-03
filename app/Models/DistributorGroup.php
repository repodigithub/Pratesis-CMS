<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorGroup extends Model
{
  const FILE_PATH = "distributor-group";
  const FILE_NAME = "MSTDISTRIBUTOR_GROUP";
  const WORKSHEET_NAME = "DISTRIBUTOR_GROUPMST";
  const FIELD_NAME = ["Kode Distributor Group", "Nama Distributor Group"];

  protected $table = "distributor_group";

  public $fillable = ["kode_distributor_group", "nama_distributor_group"];
}
