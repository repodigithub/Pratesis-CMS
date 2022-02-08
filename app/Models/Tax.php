<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
  const FILE_PATH = "tax";
  const FILE_NAME = "MSTPAJAK";
  const WORKSHEET_NAME = "PAJAKMST";
  const FIELD_NAME = ["Kode Pajak", "Nama Pajak", "Tipe Pajak", "Persentase Pajak", "Reference Pajak (CBT)"];

  protected $table = "tax";

  public $fillable = ['kode_pajak', 'nama_pajak', 'tipe_pajak', 'presentase_pajak', 'reference_tax'];
}
