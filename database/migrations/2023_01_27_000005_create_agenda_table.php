<?php

namespace Database\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'agenda';

    /**
     * Run the migrations.
     * @table agenda
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('title');
            $table->timestamp('start');
            $table->timestamp('end')->nullable()->default(null);
            $table->tinyInteger('allDay');
            $table->tinyInteger('maisDay');
            $table->char('backgroundColor', 7)->nullable()->default(null);
            $table->integer('ref_id')->nullable()->default(null);
            $table->nullableTimestamps();
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
        Schema::dropIfExists($this->tableName);
    }
};
