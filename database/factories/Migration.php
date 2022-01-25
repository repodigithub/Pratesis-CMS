<?php

namespace Database\Factories;

use Illuminate\Database\Migrations\Migration as MigrationsMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Migration extends MigrationsMigration
{
  protected function dropColumnIfExists($table, $column)
  {
    if (Schema::hasColumn($table, $column)) {
      Schema::table($table, function (Blueprint $table) use ($column) {
        $table->dropColumn($column);
      });
    }
  }
}
