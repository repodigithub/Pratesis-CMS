<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usergroup extends Model
{
  protected $table = 'user_group';

  public $fillable = ['kode_group', 'nama_group'];
}
