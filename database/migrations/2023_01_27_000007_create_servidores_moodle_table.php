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
    public $tableName = 'servidores_moodle';

    /**
     * Run the migrations.
     * @table servidores_moodle
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nome', 63);
            $table->string('url', 63);
            $table->string('nome_banco', 50);
            $table->string('ip_banco', 50);
            $table->string('ip_server', 50)->require();
            $table->string('prefixo', 10);
            $table->enum('status', ['ON', 'OFF'])->require()->default('OFF');
            $table->tinyInteger('ativo');
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
