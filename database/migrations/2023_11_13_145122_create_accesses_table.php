<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accesses', function (Blueprint $table) {
            $table->id();
            $table->integer('amo_id')->unsigned()->unique();
            $table->string('base_domain');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->string('expires');
            $table->text('api_key')->nullable();
            $table->timestamps();
        });

        Schema::table('accesses', function (Blueprint $table) {
            $table->foreign('amo_id')->references('amo_id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accesses');
    }
};
