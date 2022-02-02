<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
  protected $table = "user_group";

  protected $hidden = ["pivot"];

  public $fillable = ["kode_group", "nama_group"];
  
  public $timestamps = false;

  public function permissions()
  {
    // $related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relation
    return $this->belongsToMany(Permission::class, "group_permission", "kode_group", "kode_permission", "kode_group", "kode_permission");
  }
}
