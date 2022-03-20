<?php

namespace App\Models\Promo;

use App\Models\Area;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PromoArea extends Model
{
  const STATUS_NEW_PROMO = "new_promo";
  const STATUS_NEED_APPROVAL = "need_approval";
  const STATUS_APPROVE = "approve";
  const STATUS_REJECT = "reject";

  protected $table = "promo_area";

  public $fillable = ["opso_id", "kode_area", "budget", "status"];

  public $appends = ["nama_promo", "start_date", "end_date", "spend_type", "nama_area", "region", "alamat", "persentase", "statistics"];

  public $hidden = ["statistics", "budget_distributor"];

  public static function rules(Promo $promo, PromoArea $pa = null)
  {
    $budget = $promo->budget - $promo->budget_area;
    if (!empty($pa)) $budget += $pa->budget;

    $rules = [];
    $rules["kode_area"] = [
      "required",
      "exists:area,kode_area",
      Rule::unique("promo_area")->where(function ($query) use ($promo) {
        return $query->where("opso_id", $promo->opso_id);
      })->ignore(!empty($pa) ? $pa->id : null)
    ];
    $rules["budget"] = ["required", "numeric", "min:0", "max:$budget"];
    return $rules;
  }

  public function getNamaPromoAttribute()
  {
    return $this->promo()->first()->nama_promo;
  }

  public function getStartDateAttribute()
  {
    return $this->promo()->first()->start_date;
  }

  public function getEndDateAttribute()
  {
    return $this->promo()->first()->end_date;
  }

  public function getSpendTypeAttribute()
  {
    return $this->promo()->first()->kode_spend_type;
  }

  public function getStatisticsAttribute()
  {
    return [
      "budget" => $this->budget,
      "claim" => 0,
      "outstanding_claim" => 0,
      "budget_distributor" => $this->budget_distributor,
    ];
  }

  public function getBudgetDistributorAttribute()
  {
    try {
      return (int) $this->promoDistributors()->select(DB::raw("SUM(budget)"))->getQuery()->first()->sum ?: 0;
    } catch (\Throwable $th) {
      return 0;
    }
  }

  public function getNamaAreaAttribute()
  {
    return $this->area()->first()->nama_area;
  }

  public function getRegionAttribute()
  {
    return $this->area()->first()->region()->first()->nama_region;
  }

  public function getAlamatAttribute()
  {
    return $this->area()->first()->alamat_depo;
  }

  public function getPersentaseAttribute()
  {
    try {
      $budget = $this->promo()->first()->budget;
      return $this->budget / $budget * 100;
    } catch (\Throwable $th) {
      return 0;
    }
  }

  public function promo()
  {
    return $this->belongsTo(Promo::class, "opso_id", "opso_id");
  }

  public function area()
  {
    return $this->belongsTo(Area::class, "kode_area", "kode_area");
  }

  public function promoDistributors()
  {
    return $this->hasMany(PromoDistributor::class, "promo_area_id");
  }
}
