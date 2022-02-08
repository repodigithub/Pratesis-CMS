<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class BudgetHolder extends Model
{
  const FILE_PATH = "budget-holder";
  const FILE_NAME = "MSTBUDGET_HOLDER";
  const WORKSHEET_NAME = "BUDGET_HOLDERMST";
  const FIELD_NAME = ["Kode Budget Holder", "Nama Budget Holder"];

  protected $table = "budget_holder";

  public $fillable = ["kode_budget_holder", "nama_budget_holder"];
}
