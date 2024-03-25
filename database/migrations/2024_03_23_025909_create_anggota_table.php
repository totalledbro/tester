<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnggotaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('namadepan');
            $table->string('namablkg');
            $table->string('email')->unique(); // Unique email
            $table->string('password');
            $table->timestamp('verified_at')->nullable(); // Nullable verified_at column
            $table->integer('limit')->default(3); // Default loan limit set to 3
            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anggota');
    }
}
