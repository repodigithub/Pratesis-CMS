<?php

namespace App\Models\Promo;

use App\Models\Area;
use App\Models\Distributor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PromoDistributor extends Model
{
  const STATUS_APPROVE = "approve";
  const STATUS_CLAIM = "claim";
  const STATUS_END = "end";

  protected $table = "promo_distributor";

  public $fillable = ["promo_area_id", "kode_distributor", "budget", "status"];

  public $appends = ["nama_distributor", "distributor_group", "persentase"];

  public static function rules(PromoArea $pa, PromoDistributor $pd = null)
  {
    $budget = $pa->budget - $pa->statistics['budget_distributor'];
    if (!empty($pd)) $budget += $pd->budget;

    $rules = [];
    $rules["kode_distributor"] = [
      "required",
      "exists:distributor,kode_distributor",
      Rule::unique("promo_distributor")->where(function ($query) use ($pa, $pd) {
        return $query->where("promo_area_id", "!=", $pa->id);
      })->ignore(!empty($pd) ? $pd->id : null)
    ];
    $rules["budget"] = ["required", "numeric", "max:$budget"];
    return $rules;
  }

  public function getNamaDistributorAttribute()
  {
    try {
      return $this->distributor()->first()->nama_distributor;
    } catch (\Throwable $th) {
      return "";
    }
  }

  public function getDistributorGroupAttribute()
  {
    try {
      return $this->distributor()->first()->nama_distributor_group;
    } catch (\Throwable $th) {
      return "";
    }
  }

  public function getPersentaseAttribute()
  {
    try {
      $budget = $this->promoArea()->first()->budget;
      return $this->budget / $budget * 100;
    } catch (\Throwable $th) {
      return 0;
    }
  }

  public function promo()
  {
    return $this->promoArea()->first()->promo();
  }

  public function promoArea()
  {
    return $this->belongsTo(PromoArea::class, "promo_area_id");
  }

  public function distributor()
  {
    return $this->belongsTo(Distributor::class, "kode_distributor", "kode_distributor");
  }
}
