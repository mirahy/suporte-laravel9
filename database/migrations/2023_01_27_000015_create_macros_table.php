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
    public $tableName = 'macros';

    /**
     * Run the migrations.
     * @table macros
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nome', 31);
            $table->string('arquivo', 63)->nullable()->default(null);
            $table->string('link_servidor_moodle', 63);

            $table->index(["periodo_letivo_id"], 'periodo_letivo_id');
           
            $table->foreignId('periodo_letivo_id')
                ->constrained('periodo_letivos')
                ->onDelete('restrict')
                ->onUpdate('cascade');

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
