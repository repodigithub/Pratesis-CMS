<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
  protected $table = 'permission';

  public $fillable = ['kode_permission', 'nama_permission'];

  public function groups()
  {
    // $related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relation
    return $this->belongsToMany(Group::class, 'group_permission', 'kode_permission', 'kode_group', 'kode_permission', 'kode_group');
  }
}
