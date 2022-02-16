<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spend extends Model
{
  const FILE_PATH = "spend-type";
  const FILE_NAME = "MSTSPENDTYPE";
  const WORKSHEET_NAME = "SPENDTYPEMST";
  const FIELD_NAME = ["Kode Spend Type", "Kode Investment Type", "Fund Type", "Reference Pajak (CBT)", "Condition Type"];

  protected $hidden = ["pivot"];
  
  protected $table = "spend_type";

  public $fillable = ["kode_spend_type", "kode_investment", "fund_type", "reference_tax", "condition_type"];
}
