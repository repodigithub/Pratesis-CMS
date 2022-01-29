<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
  protected $table = 'permission';

  protected $hidden = ['pivot'];

  public $fillable = ['kode_permission', 'nama_permission'];

  public function groups()
  {
    return $this->belongsToMany(Group::class, 'group_permission', 'kode_permission', 'kode_group', 'kode_permission', 'kode_group');
  }
}
