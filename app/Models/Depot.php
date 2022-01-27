<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
  protected $table = 'area';

  public $fillable = ['kode_area', 'nama_area', 'alamat_depo', 'kode_region'];
}
