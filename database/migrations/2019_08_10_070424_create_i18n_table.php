<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateI18nTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('i18ns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('transable');
            $table->string('locale');
            $table->string('field');
            $table->text('content');
            $table->unique(['locale', 'transable_id', 'transable_type', 'field']);
            $table->index(['transable_type', 'transable_id', 'field']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('i18n');
    }
}
