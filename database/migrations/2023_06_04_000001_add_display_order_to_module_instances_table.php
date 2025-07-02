<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('module_instances', function (Blueprint $table) {
            $table->integer('display_order')->default(0)->after('is_active');
        });

        // Set default display_order values for existing module instances
        // Each instance gets a display_order based on its ID to maintain the existing order
        DB::table('module_instances')->orderBy('id')->get()->each(function ($instance, $index) {
            DB::table('module_instances')
                ->where('id', $instance->id)
                ->update(['display_order' => $index]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('module_instances', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};
