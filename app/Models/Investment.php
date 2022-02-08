<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
  const FILE_PATH = "investment";
  const FILE_NAME = "MSTINVESTMENT";
  const WORKSHEET_NAME = "INVESTMENTMST";
  const FIELD_NAME = ["Kode Investment Type", "Nama Investment Type"];

  protected $table = "investment";

  public $fillable = ["kode_investment", "nama_investment"];
}
