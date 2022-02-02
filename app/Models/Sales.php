<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
  protected $table = "sales_workforce";

  public $fillable = ["kode_sales_workforce", "nama_sales_workforce"];
}
