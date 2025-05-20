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
    public function up()
    {
        Schema::create('incoming_letters', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable();
            $table->string('sender')->nullable();
            $table->date('incoming_date')->nullable();
            $table->string('note')->nullable();
            $table->text('file')->nullable();
            $table->enum('status',['1','0']);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incoming_letters');
    }
};
