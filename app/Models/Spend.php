<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spend extends Model
{
  const FILE_PATH = "spend-type";
  const FILE_NAME = "MSTSPENDTYPE";
  const WORKSHEET_NAME = "SPENDTYPEMST";
  const FIELD_NAME = ["Kode Spend Type", "Kode Investment Type", "Fund Type", "Reference Pajak (CBT)", "Condition Type"];

  protected $table = "spend_type";

  public $fillable = ["kode_spend_type", "kode_investment", "fund_type", "reference_tax", "condition_type"];

  protected $appends = ["kegiatan"];

  protected $hidden = ["pivot"];

  public function investment()
  {
    return $this->belongsTo(Investment::class, 'kode_investment', 'kode_investment');
  }

  public function promoType()
  {
    return $this->belongsToMany(TipePromo::class, "tipe_promo_spend_type",  "kode_spend_type", "kode_kegiatan", "kode_spend_type", "kode_kegiatan");
  }

  public function getKegiatanAttribute()
  {
    try {
      return $this->promoType()->first()->only(['id', 'nama_kegiatan']);
    } catch (\Throwable $th) {
      return null;
    }
  }
}
