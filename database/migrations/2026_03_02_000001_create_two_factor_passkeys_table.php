<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwoFactorPasskeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('two_factor_passkeys', function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable', '2fa_passkey_type_id_index');
            
            // WebAuthn Credential Data
            $table->string('credential_id', 255)->unique();
            $table->text('public_key');
            $table->string('nickname')->nullable();
            
            // Security Metadata
            $table->unsignedInteger('counter')->default(0);
            $table->string('user_handle', 64)->index();
            
            $table->timestampTz('last_used_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_passkeys');
    }
}
