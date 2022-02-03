<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorGroup extends Model
{
  const FILE_PATH = "distributor-group";
  const FILE_NAME = "MSTDISTRIBUTOR_GROUP";
  const WORKSHEET_NAME = "DISTRIBUTOR_GROUPMST";

  protected $table = "sales_workforce";

  public $fillable = ["kode_sales_workforce", "nama_sales_workforce"];
}
