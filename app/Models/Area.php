<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
  public $fillable = ['kode_area', 'nama_area','alamat_depo','kode_region'];
  public $hidden = ['created_at','updated_at'];
}
