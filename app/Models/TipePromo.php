<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipePromo extends Model
{
  const FILE_PATH = "tipe-promo";
  const FILE_NAME = "MSTTIPE_PROMO_HEADER";
  const WORKSHEET_NAME = "TIPE_PROMO_HEADER";
  const FIELD_NAME = [
    "Kode Kegiatan",
    "Nama Kegiatan",
    "Deskripsi Kegiatan",
    "Kode PPN",
    "Kode PPH",
    "Kode Spend Type 1",
    "Kode Spend Type 2",
    "Kode Spend Type 3",
    "Kode Spend Type 4",
    "Kode Spend Type 5",
    "Kode Spend Type 6",
    "Kode Spend Type 7",
    "Kode Spend Type 8",
    "Kode Spend Type 9",
    "Kode Spend Type 10",
    "Kode Dokumen 1",
    "Kode Dokumen 2",
    "Kode Dokumen 3",
    "Kode Dokumen 4",
    "Kode Dokumen 5",
    "Kode Dokumen 6",
    "Kode Dokumen 7",
    "Kode Dokumen 8",
    "Kode Dokumen 9",
    "Kode Dokumen 10",
  ];
  
  protected $hidden = ["pivot"];

  protected $table = "tipe_promo";

  public $fillable = [
    "kode_kegiatan",
    "nama_kegiatan",
    "deskripsi_kegiatan",
    "kode_ppn",
    "kode_pph",
    "kode_investment",
    "file_dokumen",
  ];

  // ppn
  public function ppn()
  {
    return $this->belongsTo(Tax::class, "kode_ppn", "kode_pajak");
  }

  // pph
  public function pph()
  {
    return $this->belongsTo(Tax::class, "kode_pph", "kode_pajak");
  }

  // documents
  public function documents()
  {
    // $related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relation
    return $this->belongsToMany(DocumentClaim::class, "tipe_promo_dokumen_klaim", "kode_kegiatan", "kode_dokumen", "kode_kegiatan", "kode_dokumen");
  }

  // spend_types
  public function spendTypes()
  {
    // $related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relation
    return $this->belongsToMany(Spend::class, "tipe_promo_spend_type", "kode_kegiatan", "kode_spend_type", "kode_kegiatan", "kode_spend_type");
  }

  // investment
  public function investment()
  {
    return $this->belongsTo(Investment::class, "kode_investment", "kode_investment");
  }
}
