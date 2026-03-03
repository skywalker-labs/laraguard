<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('two_factor_trusted_devices', function (Blueprint $table) {
            $table->string('hardware_id')->nullable()->after('device_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('two_factor_trusted_devices', function (Blueprint $table) {
            $table->dropColumn('hardware_id');
        });
    }
};
