<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A régi (Drupal webform) Excel-exportok sorszáma: az import ismételhető, nem duplikál.
        Schema::table('admissions', function (Blueprint $table) {
            $table->unsignedInteger('legacy_serial')->nullable()->after('source');
            $table->unique(['source', 'locale', 'legacy_serial'], 'admissions_legacy_unique');
        });
    }

    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropUnique('admissions_legacy_unique');
            $table->dropColumn('legacy_serial');
        });
    }
};
