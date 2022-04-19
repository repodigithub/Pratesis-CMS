<?php

namespace App\Models\Promo;

use App\Models\Area;
use App\Models\Claim;
use App\Models\Distributor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PromoDistributor extends Model
{
  const STATUS_APPROVE = "approve";
  const STATUS_CLAIM = "claim";
  const STATUS_END = "end";

  protected $table = "promo_distributor";

  public $fillable = ["promo_area_id", "kode_distributor", "budget", "status"];

  public $appends = ["opso_id", "nama_promo", "thumbnail", "start_date", "end_date", "kode_spend_type", "kode_budget_holder", "document", "claim", "nama_distributor", "distributor_group", "persentase", "statistics", "status_promo", "is_claimed"];

  public $hidden = ["statistics", "budget_distributor", "status"];

  public static function rules(PromoArea $pa, PromoDistributor $pd = null)
  {
    $budget = $pa->budget - $pa->statistics['budget_distributor'];
    if (!empty($pd)) $budget += $pd->budget;
    if ($budget == 0) throw new BadRequestHttpException("error_budget_is_zero");

    $rules = [];
    $rules["kode_distributor"] = [
      "required",
      Rule::exists('distributor')->where(function ($query) use ($pa, $pd) {
        return $query->where('kode_area', $pa->kode_area);
      }),
      Rule::unique("promo_distributor")->where(function ($query) use ($pa, $pd) {
        return $query->where("promo_area_id", $pa->id);
      })->ignore(!empty($pd) ? $pd->id : null)
    ];
    $rules["budget"] = ["required", "numeric", "gt:0", "lte:$budget"];
    return $rules;
  }

  public function getStatusPromoAttribute()
  {
    $promo = $this->promo()->first();
    $claim_start = $promo->end_date;
    $claim_end = date('c', strtotime("+$promo->claim days", strtotime($claim_start)));
    if (time() > strtotime($claim_end)) {
      return self::STATUS_END;
    }
    if (time() > strtotime($claim_start)) {
      return self::STATUS_CLAIM;
    }
    return $this->status;
  }

  public function getBudgetClaimedAttribute()
  {
    return (int) Claim::selectRaw('SUM(amount)')->where('promo_distributor_id', $this->id)->getQuery()->first()->sum;
  }

  public function getIsClaimedAttribute()
  {
    return !!$this->claim()->count();
  }

  public function getOpsoIdAttribute()
  {
    return $this->promo()->first()->opso_id;
  }

  public function getThumbnailAttribute()
  {
    return $this->promo()->first()->thumbnail;
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

  public function getKodeSpendTypeAttribute()
  {
    return $this->promo()->first()->kode_spend_type;
  }

  public function getKodeBudgetHolderAttribute()
  {
    return $this->promo()->first()->kode_budget_holder;
  }

  public function getDocumentAttribute()
  {
    return $this->promo()->first()->document;
  }

  public function getClaimAttribute()
  {
    return $this->promo()->first()->claim;
  }

  public function getStatisticsAttribute()
  {
    return [
      "budget" => $this->budget,
      "claim" => $this->budget_claimed,
      "outstanding_claim" => $this->budget - $this->budget_claimed,
    ];
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

  public function claim()
  {
    return $this->hasOne(Claim::class, "promo_distributor_id");
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
