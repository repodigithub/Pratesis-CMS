<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Alasan extends Model
{
  const FILE_PATH = "alasan";
  const FILE_NAME = "MSTALASAN";
  const WORKSHEET_NAME = "ALASANMST";
  const FIELD_NAME = ["Kode Alasan", "Deskripsi Alasan"];

  protected $table = "alasan";

  public $fillable = ["kode_alasan", "deskripsi_alasan"];
}
