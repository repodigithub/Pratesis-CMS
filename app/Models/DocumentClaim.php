<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentClaim extends Model
{
  const FILE_PATH = "document-claim";
  const FILE_NAME = "MSTDOCUMENT_CLAIM";
  const WORKSHEET_NAME = "DOCUMENT_CLAIMMST";
  const FIELD_NAME = ["Kode Dokumen", "Nama Dokumen", "Sumber Dokumen"];

  protected $hidden = ["pivot"];
  
  protected $table = "dokumen_klaim";

  public $fillable = ["kode_dokumen", "nama_dokumen", "sumber_dokumen"];
}
