<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Investment extends Model
{
  use HasFactory;
  const FILE_PATH = "investment";
  const FILE_NAME = "MSTINVESTMENT";
  const WORKSHEET_NAME = "INVESTMENTMST";
  const FIELD_NAME = ["Kode Investment Type", "Nama Investment Type"];

  protected $table = "investment";

  public $fillable = ["kode_investment", "nama_investment"];
}
