<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
  protected $table = "upload_log";

  public $fillable = ["title", "type", "storage_path", "public_path", "uploader_id"];
}
