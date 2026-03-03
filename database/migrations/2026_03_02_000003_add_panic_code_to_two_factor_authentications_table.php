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
        Schema::table('two_factor_authentications', function (Blueprint $table) {
            $table->string('panic_code')->nullable()->after('shared_secret');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('two_factor_authentications', function (Blueprint $table) {
            $table->dropColumn('panic_code');
        });
    }
};
