<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
  protected $table = 'user_group';

  public $fillable = ['kode_group', 'nama_group'];

  public function permissions()
  {
    // $related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relation
    return $this->belongsToMany(Group::class, 'group_permission', 'kode_group', 'kode_permission', 'kode_group','kode_permission');
  }
}
